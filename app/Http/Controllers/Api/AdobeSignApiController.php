<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\FormTemplate;
use App;
use Storage;
use mikehaertl\pdftk\Pdf;

class AdobeSignApiController extends Controller
{
	function test(){
		$documentId = \App\Helpers\HCSU\AdobeSign\AdobeClient::uploadDocument('forms/1642788205e57d094ea2e11086942265/-373.pdf', 'Sample_Document');
		\Log::info("Document ID: {$documentId}");

		$agreementId = \App\Helpers\HCSU\AdobeSign\AdobeClient::sendDocumentForSigning($documentId);
		\Log::info("Agreement ID: {$agreementId}");

		$document = new \App\AdobeSignDocuments();

		$document->DOCUMENT_ID = $documentId;
		$document->AGREEMENT_ID = $agreementId;

		$document->save();

		// $urls = \App\Helpers\HCSU\AdobeSign\AdobeClient::getSigningURLs($agreementId);
		// \Log::info("Document ID: {$documentId}");

		// dd($urls);
	}

    function testMofa(Request $request){
        $agreementId = \App\Helpers\HCSU\AdobeSign\AdobeClient::mofaTest($request->input('case_no'));
        $signingURLs = \App\Helpers\HCSU\AdobeSign\AdobeClient::getSigningURLs($agreementId);

        $adobeSignDoc = new \App\AdobeSignDocuments();

        $adobeSignDoc->AGREEMENT_ID = $agreementId;
        $adobeSignDoc->CASE_NO = $request->input('case_no');
        $adobeSignDoc->URLS = json_encode($signingURLs);

        $adobeSignDoc->save();

        $urls = $signingURLs->signingUrlSetInfos[0]->signingUrls;

        $urlArray = [];
        foreach ($urls as $url) {
            $urlArray[$url->email] = $url->esignUrl;
        }

        return [
            'agreementId'   =>  $agreementId,
            'urls'          =>  $urlArray
        ];
    }

    function sendDocumentForSignature(Request $request){
        $case = $this->getCaseInformation($request->input('case_no'));
        $userId = $request->input('user');

        $process = $case->pro_uid;
        $processName = $request->input('process');
        $includeNV = $request->input('include_nv');
        $nvOnly = ($request->has('nvOnly')) ? true : false;
        $clientEmail = ($request->has('clientEmail')) ? $request->input('clientEmail') : null;

        $search = ['process' => $process];
        if ($nvOnly && is_null($clientEmail)) {
            $search['type'] = 'nv';
        }

        $document = FormTemplate::where($search)->first();
        $extraParams = $request->query();

        \Log::debug("Document: " . json_encode($document));

        if($document){
            $path = storage_path('app/'. $document->path);
            $data = [];
            if($document->type !== "nv"){
                $className = "\App\Helpers\HCSU\PDFTK\\" . str_replace(" ", "", $document->form_name);
                $class = new $className();
                $data = $class->getData($case->app_number, $document, $extraParams);
            }
            $creator = $case->app_init_usr_username;
            $currentUser = $case->current_task[0]->usr_name;
            $creatorFrags = ($currentUser) ? explode(" ", $currentUser) : explode(" ", $creator);

            $initials = "";
            if (count($creatorFrags) > 0) {
                if (count($creatorFrags) == 1) {
                    $initials = strtoupper($creatorFrags[0][0] . $creatorFrags[0][1]);
                }elseif (count($creatorFrags) == 2) {
                    $initials = strtoupper($creatorFrags[0][0] . $creatorFrags[1][0]);
                }elseif(count($creatorFrags) > 2){
                    $initials = strtoupper($creatorFrags[0][0] . end($creatorFrags)[0]);
                }
            }

            if ($document->ADOBE_SIGN_TEMPLATE) {
                // $noteVerbale = "note-verbals/{$processName}/Note Verbal - {$case->app_number}.pdf";
                // $documentId = null;
                $nvData = [];
                if ($includeNV || $document->type == "nv") {
                    $nvData['main_body'] = $this->getNVData($processName, $case, $initials);
                    $nvData['date'] = date("F d, Y");
                }

                $data = $data + $nvData;

                $nvTemplate = \Storage::get('adobe-sign-nv.txt');
                \Log::debug("NV Data: " . json_encode($data));
                if(($processName == "form_a" && !$nvOnly) || $processName == "form-7" && !$nvOnly){
                    $agreementId = \App\Helpers\HCSU\AdobeSign\AdobeClient::uploadMultiSignatureLibraryDocument($document->ADOBE_SIGN_TEMPLATE, $data, $case->app_number . "-" . $case->app_name, $clientEmail, null, false);
                }else{
                    \Log::debug("Process Name: " . $processName);
                    
                    $agreementId = \App\Helpers\HCSU\AdobeSign\AdobeClient::uploadLibraryDocument($document->ADOBE_SIGN_TEMPLATE, $data, $case->app_number . "-" . $case->app_name, $nvTemplate, $nvOnly);
                }
                $signingURLs = \App\Helpers\HCSU\AdobeSign\AdobeClient::getSigningURLs($agreementId);

                $adobeSignDoc = new \App\AdobeSignDocuments();

                $adobeSignDoc->AGREEMENT_ID = $agreementId;
                // $adobeSignDoc->DOCUMENT_ID = $documentId;
                $adobeSignDoc->CASE_NO = $case->app_number;
                $adobeSignDoc->URLS = json_encode($signingURLs);
                if(($processName == "form_a" && !$nvOnly) || ($processName == "form-7" && !$nvOnly)){
                    $adobeSignDoc->ROUTING = true;
                }

                $adobeSignDoc->save();

                $urls = $signingURLs->signingUrlSetInfos[0]->signingUrls;

                $urlArray = [];
                foreach ($urls as $url) {
                    $urlArray[$url->email] = $url->esignUrl;
                }

                return [
                    'agreementId'   =>  $agreementId,
                    'urls'          =>  $urlArray
                ];
            }
        }else{
            return ['message'   =>  "There is no document attached"];
        }
    }

    function sendFormAForSignature(Request $request){
    }

    function getNVData($process, $case, $initials){
        $data = new \StdClass;

        switch($process){
            case 'vat':
                $data = \App\Helpers\HCSU\Data\VATData::get($case->app_number);
                break;
            case 'blanket':
                $data = \App\Helpers\HCSU\Data\BlanketVATData::get($case->app_number);
                break;
            case 'pin':
                $data = \App\Helpers\HCSU\Data\PINData::get($case->app_number);
                break;
            case 'diplomatic-id':
                $data = \App\Helpers\HCSU\Data\DiplomaticIDData::get($case->app_number, 'new');
                break;
            case 'diplomatic-id-renewal':
                $data = \App\Helpers\HCSU\Data\DiplomaticIDData::get($case->app_number, 'renewal');
                break;
            case 'work-permit-new-case':
            case 'domestic-worker-justification':
                $data = \App\Helpers\HCSU\Data\WorkPermitExemptionData::get($case->app_number, 'new-case');
                break;
            case 'work-permit-endorsement':
                $data = \App\Helpers\HCSU\Data\WorkPermitExemptionData::get($case->app_number, 'endorsement');
                break;
            case 'work-permit-renewal':
                $data = \App\Helpers\HCSU\Data\WorkPermitExemptionData::get($case->app_number, 'renewal');
                break;
            case 'pro1a':
                $data = \App\Helpers\HCSU\Data\Pro1AData::get($case->app_number);
                break;
            case 'pro1b':
                $data = \App\Helpers\HCSU\Data\Pro1BData::get($case->app_number);
                break;
            case 'pro1c':
                $data = \App\Helpers\HCSU\Data\Pro1CData::get($case->app_number);
                break;
            case 'internship-pass':
                $data = \App\Helpers\HCSU\Data\InternshipPassData::get($case->app_number);
                break;
            case 'nod':
                $data = \App\Helpers\HCSU\Data\NODData::get($case->app_number);
                break;
            case 'form_a':
            case 'form_a_ntsa':
                $data = \App\Helpers\HCSU\Data\FormAData::get($case->app_number);
                break;
            case 'logbook':
                $data = \App\Helpers\HCSU\Data\LogbookData::get($case->app_number);
                break;
            case 'form-7':
                $data = \App\Helpers\HCSU\Data\DLData::get($case->app_number);
                break;
        }

        // dd($data);

        return (new \App\Helpers\HCSU\PDFTK\NoteVerbal($process, $data, $initials, false))->getContent();
    }

	function submitDocumentsForSigning(Request $request){
		$case = $this->getCaseInformation($request->case_no);
        $applicationType = ($request->query('type')) ? $request->query('type') : "";
        $userid = $request->query('user');
        // $variables = $this->getCaseVariables($request->case_no);
        $extraParams = $request->query();

        // dd($case);
        $process = $case->pro_uid;
        $currentTask = $case->current_task[0]->tas_uid;

        if(!$applicationType){
            $document = FormTemplate::where([
                'process'   =>  $process,
                // 'task'      =>  $currentTask
            ])->first();
        }else{
            $document = FormTemplate::where([
                'process'   =>  $process,
                // 'task'      =>  $currentTask,
                'type'      =>  $applicationType
            ])->first();
        }

        if($document){
            $path = storage_path('app/'. $document->path);
            $className = "\App\Helpers\HCSU\PDFTK\\" . str_replace(" ", "", $document->form_name);

            // $filename = "{$document->form_name} for {$client_name}, Vendor {$vat_data->supplier->SUPPLIER_NAME} on {$date}";

            $config = [];
            if (env('PDFTK_COMMAND')) {
                $config = [
                    'command'   =>  env('PDFTK_COMMAND'),
                    'useExec'   =>  true
                ];
            }

            $pdf = new Pdf($path, $config);

            $class = new $className();
            $data = $class->getData($case->app_number, $document, $extraParams);

            if ($document->ADOBE_SIGN_TEMPLATE) {
            	$agreementId = \App\Helpers\HCSU\AdobeSign\AdobeClient::uploadLibraryDocument($document->ADOBE_SIGN_TEMPLATE, $data, $case->app_name);
                // \App\Helpers\HCSU\AdobeSign\AdobeClient::addStampandSignatureFields($agreementId);
                \Log::info("Agreement ID: {$agreementId}");

                $signingURLs = \App\Helpers\HCSU\AdobeSign\AdobeClient::getSigningURLs($agreementId);

                \Log::debug("SigningURLS: " . json_encode($signingURLs));

                $document = new \App\AdobeSignDocuments();

                $document->AGREEMENT_ID = $agreementId;
                $document->CASE_NO = $case->app_number;
                $document->URLS = json_encode($signingURLs);

                $document->save();

                \Log::debug("Document successfully saved to database. Document ID: {$document->id}");

                $user = \App\Models\PM\User::where('USR_UID', $userid)->first();
                $email = $user->USR_EMAIL;

                // $emailData = $signingURLs->signingUrlSetInfos;
                $urls = $signingURLs->signingUrlSetInfos[0]->signingUrls;

				$filtered = collect($urls)->where('email', $email)->first();

				if ($filtered) {
					// die( $filtered->email );
					return \Redirect::to($filtered->esignUrl);
				}else{
					return view('pages.adobesign.status');
				}

                // 
            }
        }else{
        	abort(404);
        }
	}

	function libraryDocuments(){
		$documents = \App\Helpers\HCSU\AdobeSign\AdobeClient::getLibraryDocuments();

		return ['documents' => $documents];
	}

    function callback(Request $request){
    	\Log::debug("Callback successfully pulled in...");
    	$code = $request->query('code');
    	$api_url = $request->query('api_access_point');
    	$web_url = $request->query('web_access_point');

    	$auth = \App\Helpers\HCSU\AdobeSign\AdobeClient::auth($code, $api_url, $web_url);
    }

    function testHook(Request $request){
    	\Log::debug("Webhook Agreement Sent...");
    	\Log::debug(json_encode($request));
    	\Log::debug("Client ID: " . $request->header('X-AdobeSign-ClientId'));

    	return [
    		'xAdobeSignClientId'	=>	$request->header('X-AdobeSign-ClientId')	
    	];
    }

    function getSigningURLs(Request $request){
		// get header param to variable
		$asId = $_SERVER['HTTP_X_ADOBESIGN_CLIENTID'];
		// add headers to response for this page
		header('Content-Type: application/json');
		header('X-AdobeSign-ClientId:'. $asId );
    	\Log::debug("Webhook Agreement Sent...");
    	\Log::debug(file_get_contents('php://input'));
    	\Log::debug("Client ID: " . $request->header('X-AdobeSign-ClientId'));

    	$data = json_decode(file_get_contents('php://input'), true);

    	$urls = \App\Helpers\HCSU\AdobeSign\AdobeClient::getSigningURLs($data['agreement']['id']);
    	\Log::debug(json_encode($urls));

    	$document = \App\AdobeSignDocuments::where('AGREEMENT_ID', $data['agreement']['id'])->first();

    	$document->AGREEMENT_STATUS = $data['agreement']['status'];
    	$document->PAYLOAD = json_encode($data);
    	$document->URLS = json_encode($urls);

    	$document->save();
    }

    function testURL(){
    	$agreementId = "CBJCHBCAABAA81PBDm0ZUTWJg-ta0burtn1xkiYb5Pcn";
    	$urls = \App\Helpers\HCSU\AdobeSign\AdobeClient::getSigningURLs($agreementId);
    }

    function authCallback(Request $request){
    	\Log::debug("Auth callback called");
    }

    protected function getCaseInformation($case_no){
        if (App::environment('local') || App::environment('staging')) {
            $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no;
        }else{
            $url = "https://" . env("PM_SERVER_DOMAIN") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no;
        }
        // $url = "https://" . env("PM_SERVER_DOMAIN") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no;
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "GET", NULL, $authenticationData->access_token);
        // dd($response);

        return $response;
    }

    function getSignatories(Request $request){
        $signatories = \App\Models\AdobeSignSignatory::all();

        return $signatories;
    }

    function addSignatory(Request $request){
        $validatedData = $request->validate([
            'other_names'   =>  'required',
            'last_name'     =>  'required',
            'email'         =>  'required|unique:ADOBE_SIGN_SIGNATORIES,email'
        ]);

        $data = \App\Models\AdobeSignSignatory::create([
            'other_names'   =>  $request->input('other_names'),
            'last_name'     =>  strtoupper($request->input('last_name')),
            'email'         =>  $request->input('email'),
            'status'        =>  ($request->input('status') == "active") ? true : false 
        ]);

        return $data;
    }

    function updateSignatory(Request $request){
        $validatedData = $request->validate([
            'other_names'   =>  'required',
            'last_name'     =>  'required',
            'email'         =>  'required'
        ]);

        \App\Models\AdobeSignSignatory::where('id', $request->id)->update([
            'other_names'   =>  $request->input('other_names'),
            'last_name'     =>  strtoupper($request->input('last_name')),
            'email'         =>  $request->input('email'),
            'status'        =>  ($request->input('status') == "active") ? true : false 
        ]);
    }

    function getSignatory(Request $request){
        return \App\Models\AdobeSignSignatory::findOrFail($request->id);
    }

    function getDocumentSigningURL(Request $request){
        $signingURLs = \App\AdobeSignDocuments::where('AGREEMENT_ID', $request->agreementId)->firstOrFail();
        $urls = (json_decode($signingURLs->URLS))->signingUrlSetInfos[0]->signingUrls;
        $filtered = collect($urls)->where('email', $request->managerEmail)->first();

        if ($filtered) {
            return \Redirect::to($filtered->esignUrl);
        }
    }
}
