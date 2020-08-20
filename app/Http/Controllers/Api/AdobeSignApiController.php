<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\FormTemplate;
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

    function testMofa(){
        $agreementId = \App\Helpers\HCSU\AdobeSign\AdobeClient::mofaTest(455);
        
    }

    function sendDocumentForSignature(Request $request){
        $case = $this->getCaseInformation($request->input('case_no'));
        $userId = $request->input('user');

        $process = $case->pro_uid;

        $document = FormTemplate::where('process',$process)->first();
        $extraParams = $request->query();

        if($document){
            $path = storage_path('app/'. $document->path);
            $className = "\App\Helpers\HCSU\PDFTK\\" . str_replace(" ", "", $document->form_name);
            $class = new $className();
            $data = $class->getData($case->app_number, $document, $extraParams);
            if ($document->ADOBE_SIGN_TEMPLATE) {
                $agreementId = \App\Helpers\HCSU\AdobeSign\AdobeClient::uploadLibraryDocument($document->ADOBE_SIGN_TEMPLATE, $data, $case->app_name);
                $signingURLs = \App\Helpers\HCSU\AdobeSign\AdobeClient::getSigningURLs($agreementId);

                $adobeSignDoc = new \App\AdobeSignDocuments();

                $adobeSignDoc->AGREEMENT_ID = $agreementId;
                $adobeSignDoc->CASE_NO = $case->app_number;
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
        }
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

		// dd($documents);
		echo "<ul>";
		foreach ($documents->libraryDocumentList as $document) {
			echo "<li>{$document->name} - {$document->libraryDocumentId}</li>";
		}
		echo "</ul>";
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
        $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no;
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "GET", NULL, $authenticationData->access_token);
        // dd($response);

        return $response;
    }
}
