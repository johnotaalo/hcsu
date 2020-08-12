<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

	function libraryDocuments(){
		$documents = \App\Helpers\HCSU\AdobeSign\AdobeClient::getLibraryDocuments();

		// dd($documents);
		echo "<ul>";
		foreach ($documents->libraryDocumentList as $document) {
			echo "<li>{$document->name} - {$document->id}</li>";
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
}
