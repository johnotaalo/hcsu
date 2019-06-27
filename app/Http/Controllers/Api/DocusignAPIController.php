<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DocusignAPIController extends Controller
{
	function login(){
		$provider = \App\Helpers\HCSU\PMDocusign::getProvider();
		$authorizationUrl = $provider->getAuthorizationUrl();
		$state = $provider->getState();

		$data['state'] = $state;
		\App\Helpers\HCSU\PMDocusign::updateAuthData($data);
		return \Redirect::away($authorizationUrl);
	}

	function loginCallback(Request $request){
		$provider = \App\Helpers\HCSU\PMDocusign::getProvider();

		$code = $request->query('code');
		$state = $request->query('state');

		$current_session = \App\Helpers\HCSU\PMDocusign::getAuthData();
		$state_session = $current_session->state;

		if(empty($state) || (isset($state_session) && $state !== $state_session)){
			if (isset($state_session)){
				$current_session['state'] = "";
			}
			exit('Invalid OAuth state');
		}else{
			try{
				$accessToken = $provider->getAccessToken('authorization_code', ['code' => $code] );
				\Log::debug("Docusign Authentication Complete");

				$user = $provider->getResourceOwner($accessToken);
				$account_info = $user->getAccountInfo();

				$base_uri_suffix = '/restapi';

				$session_data = [
					'ds_access_token'	=>	$accessToken->getToken(),
					'ds_refresh_token'	=>	$accessToken->getRefreshToken(),
					'ds_expiration'		=>	$accessToken->getExpires(),
					'ds_user_name'		=>	$user->getName(),
					'ds_user_email'		=>	$user->getEmail(),
					'ds_account_id'		=>	$account_info["account_id"],
					'ds_account_name'	=>	$account_info["account_name"],
					'ds_base_path'		=>	$account_info["base_uri"] . $base_uri_suffix
				];

				$current_data = (object)array_merge((array)$current_session, $session_data);

				\App\Helpers\HCSU\PMDocusign::updateAuthData($current_data);

				return redirect()->route('default', ['any' => '']);
			}catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
				exit($e->getMessage());
			}
		}
	}

	function generateSigningDocument(Request $request){
		if(!\App\Helpers\HCSU\PMDocusign::checkExpiry()){
			\App\Helpers\HCSU\PMDocusign::refreshToken();
		}
		$user = $request->query("user");
		$process = $request->query("process");
		$task = $request->query('task');
		$case = $request->query('case');

		$vat_data = \App\Models\VAT::where('CASE_NO', $case)->first();
		$name = "";
		$firstIDChar = substr($vat_data->HOST_COUNTRY_ID, 0, 1);

		$goodsArray = ($vat_data->invoices)->map(function($invoice){
			return $invoice->GOODS_DESCRIPTION;
		})->toArray();

		$invoiceNumbersArray = ($vat_data->invoices)->map(function($invoice){
			return $invoice->DOCUMENT_NO;
		})->toArray();

		$vatAmounts = ($vat_data->invoices)->map(function($invoice){
			return $invoice->VAT_AMOUNT;
		})->toArray();

		$goods = $this->generateCombinedString($goodsArray);
		$invoice_numbers = $this->generateCombinedString($invoiceNumbersArray);
		$totalVAT = ($vat_data->invoices)->sum('VAT_AMOUNT');


		if($firstIDChar == "1"){
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$vat_data->HOST_COUNTRY_ID})"))->first();
			$principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $vat_data->HOST_COUNTRY_ID)->first();
			$name = ucwords(strtolower($principal->OTHER_NAMES)) . " " . strtoupper($principal->LAST_NAME);
		}

		// 	Don't forget to include type check here
		

		$tabData = [
			'ref_no'			=>	$vat_data->EXEMPTION_FORM_REF_NO,
			'mission'			=>	$contract->ACRONYM,
			'date'				=>	date('F d, Y', strtotime($vat_data->CREATED_AT)),
			'name_designation'	=>	$name . ", " . $contract->DESIGNATION,
			'supplier'			=>	$vat_data->supplier->SUPPLIER_NAME,
			'supplier_address'	=>	$vat_data->supplier->SUPPLIER_ADDRESS,
			'supplier_pin'		=>	$vat_data->supplier->PIN,
			'goodsDescription'	=>	$goods,
			'invoice_numbers'	=>	$invoice_numbers,
			'total_vat'			=>	number_format($totalVAT, 2)
		];

		// echo "<pre>";print_r($tabData);die;

		$accountData = \App\Helpers\HCSU\PMDocusign::getAuthData();

		$sql = "SELECT * FROM USERS WHERE USR_UID = '{$user}'";
		$user = collect(\DB::connection('pm')->select($sql))->first();

		$signer_name = "{$user->USR_FIRSTNAME} {$user->USR_LASTNAME}";
		$signer_email = $user->USR_EMAIL;

		$template_id = \App\Helpers\HCSU\PMDocusign::getTemplate($process, $task, $signer_name);
		$class = \App\Helpers\HCSU\PMDocusign::getTemplateClass($process, $task);

		$envelope_args = [
			'signer_email' => $signer_email,
			'signer_name' => $signer_name,
			'signer_client_id' => 1000,
			'ds_return_url' => url("/docusign/afterSignature/{$case}/{$process}/{$task}"),
			'template_id' => $template_id
		];

		$args = [
			'account_id' => $accountData->ds_account_id,
			'base_path' => $accountData->ds_base_path,
			'ds_access_token' => $accountData->ds_access_token,
			'envelope_args' => $envelope_args
		];

		try {
			$results = $this->worker($args, $class, $tabData);
		}catch (\DocuSign\eSign\ApiException $e) {
			$error_code = $e->getResponseBody()->errorCode;
			$error_message = $e->getResponseBody()->message;
			die($error_message);
		}
		
		if ($results) {
			// $_SESSION["envelope_id"] = $results["envelope_id"]; # Save for use by other examples
			# which need an envelopeId
			$docusignData = \App\Models\DocusignEnvelope::updateOrCreate(
				["CASE_NUMBER"	=>	$case,
					"TASK"			=>	$task,
					"PROCESS"		=>	$process
				],
				["CASE_NUMBER"	=>	$case, 
					"ENVELOPE_ID"	=>	$results["envelope_id"], 
					"TASK"			=>	$task,
					"PROCESS"		=>	$process
				]);

			# Redirect the user to the Signing Ceremony
			# Don't use an iFrame!
			# State can be stored/recovered using the framework's session or a
			# query parameter on the returnUrl (see the makeRecipientViewRequest method)
			return \Redirect::away($results["redirect_url"]);
		}
	}

	function downloadDocument(Request $request){
		if(!\App\Helpers\HCSU\PMDocusign::checkExpiry()){
			\App\Helpers\HCSU\PMDocusign::refreshToken();
		}
		$envelope_id = $request->envelope_id;
		$envelope = \App\Models\DocusignEnvelope::where('envelope_id', $envelope_id)->first();

		$document = \App\Helpers\HCSU\PMDocusign::getSignedDocument($envelope_id, $envelope->CASE_NUMBER);

		// \Storage::disk('local')->put('file.txt', $document['data']);

		if($document){
			header("Content-Type: {$document['mimetype']}");
			header("Content-Disposition: attachment; filename=\"{$document['doc_name']}\"");
			ob_clean();
			flush();
			$file_path = $document['data']->getPathname();
			readfile($file_path);
			exit();
		}
	}

	function index(){
		$expiry = \App\Helpers\HCSU\PMDocusign::checkExpiry();
	  	if(!$expiry){
		  	$provider = $this->getProvider();
		    $authorizationUrl = $provider->getAuthorizationUrl();
		    $state = $provider->getState();

		    session(['oauth2state'	=>	$state]);

		    return \Redirect::away($authorizationUrl);
		}else{
			$signer_name = preg_replace('/([^\w \-\@\.\,])+/', '', env('DOCUSIGN_SIGNER_NAME'));
			$signer_email = preg_replace('/([^\w \-\@\.\,])+/', '', env('DOCUSIGN_SIGNER_EMAIL'));
			// echo $signer_email;die;
			$template_id = "6599bd4e-b0e1-44c5-86af-e980521d1469";

			$envelope_args = [
				'signer_email' => $signer_email,
				'signer_name' => $signer_name,
				'signer_client_id' => 1000,
				'ds_return_url' => url('/docusign/afterSignature'),
				'template_id' => $template_id
			];

			$args = [
				'account_id' => \Session::get('ds_account_id'),
				'base_path' => \Session::get('ds_base_path'),
				'ds_access_token' => \Session::get('ds_access_token'),
				'envelope_args' => $envelope_args
			];
			// die($args['account_id']);

			try{
				$results = $this->worker($args);
			}catch (\DocuSign\eSign\ApiException $e) {
				$error_code = $e->getResponseBody()->errorCode;
				$error_message = $e->getResponseBody()->message;
				die($error_message);
			}

			if ($results) {
				$_SESSION["envelope_id"] = $results["envelope_id"]; # Save for use by other examples
				# which need an envelopeId

				# Redirect the user to the Signing Ceremony
				# Don't use an iFrame!
				# State can be stored/recovered using the framework's session or a
				# query parameter on the returnUrl (see the makeRecipientViewRequest method)
				return \Redirect::away($results["redirect_url"]);
			}
		}
  }

	function worker($args, $class, $tabData){
		$envelope_args = $args["envelope_args"];
		// echo "<pre>";print_r(\Session::get('ds_account_id'));die;
		$envelope_definition = $this->make_envelope($envelope_args, $class, $tabData);
	
		$config = new \DocuSign\eSign\Configuration();
		$config->setHost($args['base_path']);
		$config->addDefaultHeader('Authorization', 'Bearer ' . $args['ds_access_token']);
		$api_client = new \DocuSign\eSign\Client\ApiClient($config);
		$envelope_api = new \DocuSign\eSign\Api\EnvelopesApi($api_client);
		$results = $envelope_api->createEnvelope($args['account_id'], $envelope_definition);
		$envelope_id = $results->getEnvelopeId();
		// die($envelope_id);
		$recipients = $envelope_api->listRecipients($args['account_id'], $envelope_id);
		// echo "<pre>";print_r($recipients);die;
		$recipient_guid = $recipients->getSigners()[0]->getRecipientIdGuid();
		$authentication_method = 'None';

		// echo "<pre>";print_r($envelope_args);die;
		
		$recipient_view_request = new \DocuSign\eSign\Model\RecipientViewRequest([
			'authentication_method' => $authentication_method,
			'client_user_id' => $envelope_args['signer_client_id'],
			'recipient_id' => '1',
			'return_url' => $envelope_args['ds_return_url'],
			'user_name' => $envelope_args['signer_name'], 'email' => $envelope_args['signer_email']
		]);

		// echo "<pre>";print_r($recipient_view_request);die;
		try{
			$results = $envelope_api->createRecipientView($args['account_id'], $envelope_id, $recipient_view_request);
			// echo "<pre>";print_r($results);die;
		}catch(Exception $ex){
			die($ex->getMessage());
		}

		return ['envelope_id' => $envelope_id, 'redirect_url' => $results['url']];
	}

	function make_envelope($args, $class, $data){
		$envelope_definition = new \DocuSign\eSign\Model\EnvelopeDefinition(['status' => 'sent', 'template_id' => $args['template_id']]);

		$referenceTab = $class->referenceTab($data['ref_no']);
		$missionTab = $class->missionTab($data['mission']);
		$dateTab = $class->dateTab($data['date']);
		$nameTitleTab = $class->nameTitleTab($data['name_designation']);
		$supplierNameTab = $class->supplierNameTab($data['supplier']);
		$supplierAddressTab = $class->supplierAddressTab($data['supplier_address']);
		$supplierPINTab = $class->supplierPINTab($data['supplier_pin']);
		$goodsTab = $class->goodsTab($data['goodsDescription']);
		$invoiceTab = $class->invoiceNumberTab($data['invoice_numbers']);
		$vatTab = $class->totalVATTab($data['total_vat']);

		$tabs = new \DocuSign\eSign\Model\Tabs([
			'text_tabs' => [$referenceTab, $missionTab, $dateTab, $nameTitleTab, $supplierNameTab, $supplierAddressTab, $supplierPINTab, $goodsTab, $invoiceTab, $vatTab]
		]);

		$signer = new \DocuSign\eSign\Model\TemplateRole([
			'email' => $args['signer_email'], 'name' => $args['signer_name'],
			'role_name' => 'signer',
			'client_user_id' => $args['signer_client_id'],
			'tabs'	=>	$tabs
		]);

		$envelope_definition->setTemplateRoles([$signer]);

		// echo "<pre>";print_r($envelope_definition);die;

		return $envelope_definition;
	}

	function afterSignature(Request $request){
		if(!\App\Helpers\HCSU\PMDocusign::checkExpiry()){
			\App\Helpers\HCSU\PMDocusign::refreshToken();
		}
		$case = $request->case;
		$process = $request->process;
		$task = $request->task;

		$envelope = \App\Models\DocusignEnvelope::where(['CASE_NUMBER'=>$case, 'PROCESS' => $process, 'TASK'	=>	$task])->first();

		$envelope_id = $envelope->ENVELOPE_ID;

		$status = \App\Helpers\HCSU\PMDocusign::getEnvelopeStatus($envelope_id);

		$envelope->STATUS = $status->status;
		$envelope->LAST_MODIFIED = $status->last_modified;

		$envelope->save();

		return view("pages.documents.status")->with(['status' => $status->status, 'envelope_id'	=>	$envelope_id]);
	}

	function getProvider(){
		$provider = new \App\Helpers\HCSU\DocuSign([
			'clientId' => env('DOCUSIGN_CLIENT_ID'),
			'clientSecret' => env('DOCUSIGN_CLIENT_SECRET'),
			'redirectUri' => url('/docusign/callback'),
			'authorizationServer' => env('DOCUSIGN_AUTHORIZATION_SERVER'),
			'allowSilentAuth' => env('DOCUSIGN_ALLOW_SILENT_AUTH')
		]);

		return $provider;
	}

	function authenticateDocuSign(){

	}

	function docusignCallback(Request $request){
		$provider = $this->getProvider();

		$code = $request->query('code');
		$state = $request->query('state');

		$state_session = \Session::get('oauth2state');

		if(empty($state) || (isset($state_session) && $state !== $state_session)){
			if (isset($state_session)){
				\Session::forget('oauth2state');
			}
			exit('Invalid OAuth state');
		}else{
			try{
				$accessToken = $provider->getAccessToken('authorization_code', ['code' => $code] );
				$request->session()->flash('status', 'Authentication Complete');

				$user = $provider->getResourceOwner($accessToken);
				$account_info = $user->getAccountInfo();

				$base_uri_suffix = '/restapi';

				$session_data = [
					'ds_access_token'	=>	$accessToken->getToken(),
					'ds_refresh_token'	=>	$accessToken->getRefreshToken(),
					'ds_expiration'		=>	$accessToken->getExpires(),
					'ds_user_name'		=>	$user->getName(),
					'ds_user_email'		=>	$user->getEmail(),
					'ds_account_id'		=>	$account_info["account_id"],
					'ds_account_name'	=>	$account_info["account_name"],
					'ds_base_path'		=>	$account_info["base_uri"] . $base_uri_suffix
				];

				session($session_data);

				return redirect()->route('docusign-client');
			}catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
				exit($e->getMessage());
			}
		}
	}

	function checkToken(){
		$accessToken = \Session::get('ds_access_token');
		$expiration = \Session::get('ds_expiration');

		$ok = isset($accessToken) && isset($expiration);
		$ok = $ok && (($expiration - (60 * 60)) > time());

		return $ok;
	}

	function clearSession(){
		\Session::forget('ds_access_token');
		\Session::forget('ds_refresh_token');
		\Session::forget('ds_user_email');
		\Session::forget('ds_user_name');
		\Session::forget('ds_expiration');
		\Session::forget('ds_account_id');
		\Session::forget('ds_account_name');
		\Session::forget('ds_base_path');
		\Session::forget('envelope_id');
		\Session::forget('envelope_documents');
		\Session::forget('template_id');
	}

	function generateCombinedString($array){
		if(count($array) == 1){
			return $array[0];
		}else{
			$last = array_pop($array);
			$combined = implode(", ", $array) . " & " . $last;
			return $combined;
		}
	}
}
