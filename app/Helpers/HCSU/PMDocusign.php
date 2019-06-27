<?php

namespace App\Helpers\HCSU;

class PMDocusign{
	public static $authFile = "docusign_auth.json";

	function refreshToken(){
		$expiry = (new self)::checkExpiry();
		var_dump($expiry);die;
	}

	public static function getProvider(){
		$provider = new \App\Helpers\HCSU\DocuSign([
					'clientId' => env('DOCUSIGN_CLIENT_ID'),
					'clientSecret' => env('DOCUSIGN_CLIENT_SECRET'),
					'redirectUri' => url('/docusign/callback'),
					'authorizationServer' => env('DOCUSIGN_AUTHORIZATION_SERVER'),
					'allowSilentAuth' => env('DOCUSIGN_ALLOW_SILENT_AUTH')
		]);

		return $provider;
	}

	public static function getAuthData(){
		$file = self::$authFile;
		if(\Storage::disk('local')->exists($file)){
			return json_decode(\Storage::get($file));
		}
		return [];
	}

	public static function updateAuthData($data){
		$file = self::$authFile;
		if(!\Storage::disk('local')->exists($file)){
			\Storage::disk('local')->put($file, json_encode($data));
		}else{
			$current_data = json_decode(\Storage::get($file));
			foreach ($data as $k => $d) {
				$current_data->$k = $d;
			}
			\Storage::disk('local')->put($file, json_encode($current_data));
		}
	}

	public static function checkExpiry(){
		$authData = (new self)::getAuthData();
		$accessToken = \Session::get('ds_access_token');
		$expiration = \Session::get('ds_expiration');

		$ok = isset($accessToken) && isset($expiration);
		$ok = $ok && (($expiration - (60 * 60)) > time());

		return $ok;
	}

	public static function getTemplate($process, $task, $user = null){
		$template = config("docusign.templates.{$process}.{$task}.id.{$user}");

		return $template;
	}

	public static function getTemplateClass($process, $task, $user = null){
		$class = config("docusign.templates.{$process}.{$task}.class");

		return $class;
	}

	public static function worker($args){
		$envelope_args = $args["envelope_args"];
		// echo "<pre>";print_r(\Session::get('ds_account_id'));die;
		$envelope_definition = (new self)->make_envelope($envelope_args);
	
		$config = new \DocuSign\eSign\Configuration();
		$config->setHost($args['base_path']);
		$config->addDefaultHeader('Authorization', 'Bearer ' . $args['ds_access_token']);
		$api_client = new \DocuSign\eSign\Client\ApiClient($config);
		$envelope_api = new \DocuSign\eSign\Api\EnvelopesApi($api_client);
		$results = $envelope_api->createEnvelope($args['account_id'], $envelope_definition);
		$envelope_id = $results->getEnvelopeId();
		// die($envelope_id);
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

	public static function make_envelope($args){
		$envelope_definition = new \DocuSign\eSign\Model\EnvelopeDefinition(['status' => 'sent', 'template_id' => $args['template_id']]);

		$referenceTab = new \DocuSign\eSign\Model\Text([
            'tab_label' => "reference-number", 'value' => 'Sample Reference Number']);

		$tabs = new \DocuSign\eSign\Model\Tabs([
			'text_tabs' => [$referenceTab]
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

	public static function getEnvelopeStatus($envelope_id){
		$account = self::getAuthData();

		try {
			$envelope_api = self::getEnvelopeApi();
			$results = $envelope_api->getEnvelope($account->ds_account_id, $envelope_id);
		} catch (\DocuSign\eSign\ApiException $e) {
			$error_code = $e->getResponseBody()->errorCode;
			$error_message = $e->getResponseBody()->message;
			
			exit($error_message);
		}

		if ($results) {
			$status = $results->getStatus();
			$last_modified = $results->getStatusChangedDateTime();
			$converted_time = date("Y-m-d h:i:s", strtotime($last_modified));

			return (object)["status" => $status, "last_modified" => $converted_time];
		}

		return [];
	}

	public static function getEnvelopeDocuments($envelope_id){
		$account = self::getAuthData();

		try {
			$envelope_api = self::getEnvelopeApi();
			$results = $envelope_api->listDocuments($account->ds_account_id, $envelope_id);
		} catch (\DocuSign\eSign\ApiException $e) {
			$error_code = $e->getResponseBody()->errorCode;
			$error_message = $e->getResponseBody()->message;
			
			exit($error_message);
		}

		if($results){
			return $results;
		}
		return [];
	}

	public static function getSignedDocument($envelope_id, $doc_name){
		$account = self::getAuthData();

		$documents = self::getEnvelopeDocuments($envelope_id);

		$documents = collect($documents->getEnvelopeDocuments())->map(function($document){
			if($document->getDocumentId() == 1){
				return ['document_id' => $document->getDocumentId(), 'name'	=>	$document->getName(), 'type' => $document->getType()];
			}
		})
		->reject(function($document){
			return empty($document);
		})->toArray();

		try {
			$envelope_api = self::getEnvelopeApi();
			
			$temp_file = $envelope_api->getDocument($account->ds_account_id, '1', $envelope_id);
			$document = $documents[0];

			$document_name = $document['name'];
			$has_pdf_suffix = strtoupper(substr($document_name, -4)) == '.PDF';

			if ($document["type"] == "content") {
				$doc_name .= ".pdf";
				$pdf_file = true;
			}

			if ($pdf_file) {
				$mimetype = 'application/pdf';
			}

			return ['mimetype' => $mimetype, 'doc_name' => $doc_name, 'data' => $temp_file];

		} catch (\DocuSign\eSign\ApiException $e) {
			$error_code = $e->getResponseBody()->errorCode;
			$error_message = $e->getResponseBody()->message;
			
			exit($error_message);
		}
	}

	public static function getConfig(){
		$account = self::getAuthData();

		$config = new \DocuSign\eSign\Configuration();

		$config->setHost($account->ds_base_path);
		$config->addDefaultHeader('Authorization', 'Bearer ' . $account->ds_access_token);

		return $config;
	}

	public static function getEnvelopeApi(){
		$config = self::getConfig();

		$api_client = new \DocuSign\eSign\Client\ApiClient($config);
		$envelope_api = new \DocuSign\eSign\Api\EnvelopesApi($api_client);

		return $envelope_api;
	}
}