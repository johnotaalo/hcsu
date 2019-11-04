<?php

namespace App\Http\Controllers\FocalPoints;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\VATUserApplication;
use App\Rules\Documentno;

class VATController extends Controller
{
	function addVATApplication(Request $request){
		// $request->
		$validatedData = $request->validate([
			'supplier'						=>	'required',
			'client'						=>	'required',
			'documents'						=>	'required|array|min:1',
			'documents.*.documentNo'		=>	['required', new Documentno($request->supplier)],
			'documents.*.documentDate'		=>	'required',
			'documents.*.goodsDescription'	=>	'required',
			'documents.*.vatAmount'			=>	'required',
			'documents.*.documentType'		=>	'required',
			'documents.*.invoiceFile'		=>	'required|mimes:pdf'
		]);

		$documentPaths = [];

		$process = $outputDocument = "";

		try{
			$processes = json_decode(\Storage::get("processes.json"));
			$process = $processes->VAT;

			if(!$process){
				return response()->json(['error'=>'Could not retrieve process'], 500);
			}
		}catch(Exception $ex){
			return response()->json(['error'=>'Could not retrieve process'], 500);
		}

		if($request->client['HOST_COUNTRY_ID'][0] == 3){

		}

		$outputDocument = $process->documents->output;
		$inputDocument = $process->documents->input;

		$vatData = [
			'host_country_id'			=>	$request->client['HOST_COUNTRY_ID'],
			'host_country_id_label'		=>	$request->client['HOST_COUNTRY_ID'],
			'supplier'					=>	$request->supplier['ID'],
			'supplier_label'			=>	$request->supplier['SUPPLIER_NAME'],
			'acknowledgementDate'		=>	date('Y-m-d'),
			'clientInitiated'			=>	1,
			'clientInitiated_label'		=>	'True',
			'comments'					=>	"Submitted by " . \Auth::user()->name . " Focal Point " . \Auth::user()->focal_point->agency->ACRONYM . " through the external application"
		];

		// dd($vatData);

		$grid = [];

		$k = 1;
		foreach ($request->documents as $key => $document) {
			$document['vatAmount'] = str_replace(',', '', $document['vatAmount']);
			$document['vatAmount'] = str_replace(' ', '', $document['vatAmount']);

			$grid[$k] = [
				'documentType'			=>	$document['documentType'],
				'documentType_label'	=>	ucwords($document['documentType']),
				'documentNo'			=>	$document['documentNo'],
				'documentDate'			=>	date('Y-m-d', strtotime($document['documentDate'])),
				'goodsServices'			=>	$document['goodsDescription'],
				'vatAmount'				=>	$document['vatAmount'],
				'etrNo'					=>	""
			];

			// dd($document);

			$filePath = $document['invoiceFile']->store('uploads/focal-points');
			$documentPaths[] = storage_path("app/{$filePath}");
			$k++;
		}

		$vatData['documentDetails'] = $grid;

		// dd($vatData);

		$authenticationData = json_decode(\Storage::get("pmauthentication.json"));
		$url = "http://".env('PM_SERVER')."/api/1.0/workflow/cases";

		$data = [
			'pro_uid'   => $process->id,
			'tas_uid'   => $process->task
			// 'variables'	=> $vatData
		];

		$response = \Processmaker::executeREST($url, "POST", $data, $authenticationData->access_token);
		// dd($response);

		$case_no = $response->app_number;
		$app_uid = $response->app_uid;

		$setVariablesURL = "http://".env('PM_SERVER')."/api/1.0/workflow/cases/{$app_uid}/variable";
		$response = \Processmaker::executeREST($setVariablesURL, "PUT", $vatData, $authenticationData->access_token);
		// dd($response);

		$variables_url = "http://".env('PM_SERVER')."/api/1.0/workflow/cases/{$app_uid}/variables";
		$steps_url = "http://".env('PM_SERVER')."/api/1.0/workflow/project/{$process->id}/activity/{$process->task}/steps";
		$outputDocumentURL = "http://".env('PM_SERVER')."/api/1.0/workflow/extrarest/case/$app_uid/output-document/{$outputDocument}";
		$inputDocumentURL = "http://".env('PM_SERVER')."/api/1.0/workflow/cases/{$app_uid}/input-document";

		$stepsResponse = \Processmaker::executeREST($steps_url, "GET", [], $authenticationData->access_token);
		foreach ($stepsResponse as $step) {
			// Run triggers
			foreach ($step->triggers as $trigger) {
				$triggerURL = "http://".env('PM_SERVER')."/api/1.0/workflow/cases/{$app_uid}/execute-trigger/{$trigger->tri_uid}";
				$triggerResponse = \Processmaker::executeREST($triggerURL, "PUT", [], $authenticationData->access_token);
			}
		}

		$inputDocumentIDs = [];
		if ($inputDocument) {
			foreach ($documentPaths as $key => $path) {
				$inputData = [
					'inp_doc_uid'		=>	$inputDocument,
					'tas_uid'			=>	$process->task,
					'app_doc_comment'	=>	"Document uploaded by: " . \Auth::user()->name . " Focal Point " . \Auth::user()->focal_point->agency->ACRONYM . " through the external application",
					'form'            	=> new \CurlFile($path)
				];

				$inputDocumentRes = \Processmaker::executeREST($inputDocumentURL, "POST", $inputData, $authenticationData->access_token, true);
				$inputDocumentIDs[$key] = $inputDocumentRes->app_doc_uid;
			}
		}

		// $response = \Processmaker::executeREST($outputDocumentURL, "POST", null, $authenticationData->access_token);
		// dd($variables_url);

		// $response = \Processmaker::executeREST($steps_url, "GET", [], $authenticationData->access_token);
		$outputDocRes = \Processmaker::executeREST($outputDocumentURL, "POST", [], $authenticationData->access_token);
		$response = \Processmaker::routeCase($app_uid);
		

		if (empty($response)) {
			$base_url = "http://".env('PM_SERVER_DOMAIN')."/sysworkflow/en/neoclassic/{$outputDocRes->app_doc_link}";

			$userApplication = new \App\VATUserApplication();

			$userApplication->CASE_NO = $case_no;
			$userApplication->ACKNOWLEDGEMENT_LINK = $base_url;
			$userApplication->USER_ID = \Auth::user()->id;

			$userApplication->save();

			foreach ($documentPaths as $key => $path) {
				\App\VATUserApplicationDocument::create([
					'APPLICATION_ID'	=>	$userApplication->id,
					'PATH'				=>	$path,
					'DOCUMENT_UID'		=>	$inputDocumentIDs[$key]
				]);
			}

			\Mail::to($userApplication->user->focal_point->EMAIL)->send(new \App\Mail\AcknowledgeVATReceipt($userApplication));

			return [
				'case_no'			=>	$case_no,
				// 'link'				=>	$base_url,
				// 'document_link'		=>	$outputDocRes->app_doc_link,
				'application_id'	=>	$userApplication->id
			];
		}else{
			return \Response::json($response, 500);
		}
	}

	function updateVATApplication(Request $request){
		$case = \App\Models\PM\Application::select('APP_UID', 'APP_NUMBER')->where('APP_NUMBER', $request->case_no)->first();
		$authenticationData = json_decode(\Storage::get("pmauthentication.json"));

		// dd($case);
		// dd($request->input());
		$validatedData = $request->validate([
			'supplier'						=>	'required',
			'client'						=>	'required',
			'documents'						=>	'required|array|min:1',
			'documents.*.documentNo'		=>	['required', new Documentno($request->supplier, true, $request->case_no)],
			'documents.*.documentDate'		=>	'required',
			'documents.*.goodsDescription'	=>	'required',
			'documents.*.vatAmount'			=>	'required',
			'documents.*.documentType'		=>	'required',
			'documents.*.invoiceFile'		=>	'sometimes|required|mimes:pdf'
		]);

		$documentPaths = [];

		$process = $outputDocument = "";

		try{
			$processes = json_decode(\Storage::get("processes.json"));
			$process = $processes->VAT;

			if(!$process){
				return response()->json(['error'=>'Could not retrieve process'], 500);
			}
		}catch(Exception $ex){
			return response()->json(['error'=>'Could not retrieve process'], 500);
		}

		$outputDocument = $process->documents->output;
		$inputDocument = $process->documents->input;

		// URLs
		$setVariablesURL = "http://".env('PM_SERVER')."/api/1.0/workflow/cases/{$case->APP_UID}/variable";
		$steps_url = "http://".env('PM_SERVER')."/api/1.0/workflow/project/{$process->id}/activity/{$process->task}/steps";
		$inputDocumentURL = "http://".env('PM_SERVER')."/api/1.0/workflow/cases/{$case->APP_UID}/input-document";

		$vatData = [
			'host_country_id'			=>	$request->client['HOST_COUNTRY_ID'],
			'host_country_id_label'		=>	$request->client['HOST_COUNTRY_ID'],
			'supplier'					=>	$request->supplier['ID'],
			'supplier_label'			=>	$request->supplier['SUPPLIER_NAME'],
			'acknowledgementDate'		=>	date('Y-m-d'),
			'clientInitiated'			=>	1,
			'clientInitiated_label'		=>	'True',
			'comments'					=>	"Submitted by " . \Auth::user()->name . " Focal Point " . \Auth::user()->focal_point->agency->ACRONYM . " through the external application"
		];

		$grid = [];

		$k = 1;
		foreach ($request->documents as $key => $document) {
			$document['vatAmount'] = str_replace(',', '', $document['vatAmount']);
			$document['vatAmount'] = str_replace(' ', '', $document['vatAmount']);

			$grid[$k] = [
				'documentType'			=>	$document['documentType'],
				'documentType_label'	=>	ucwords($document['documentType']),
				'documentNo'			=>	$document['documentNo'],
				'documentDate'			=>	date('Y-m-d', strtotime($document['documentDate'])),
				'goodsServices'			=>	$document['goodsDescription'],
				'vatAmount'				=>	$document['vatAmount'],
				'etrNo'					=>	""
			];

			// dd($document);
			if(isset($document['invoiceFile'])){
				$filePath = $document['invoiceFile']->store('uploads/focal-points');
				$documentPaths[] = storage_path("app/{$filePath}");
			}

			if(!isset($document['invoiceFile']) && $document['edit']){
				$doc = \App\VATUserApplicationDocument::find($document['id']);
				if($doc)
					$documentPaths[] = $doc->PATH;
			}
			$k++;
		}

		$vatData['documentDetails'] = $grid;
		
		$response = \Processmaker::executeREST($setVariablesURL, "PUT", $vatData, $authenticationData->access_token);

		$stepsResponse = \Processmaker::executeREST($steps_url, "GET", [], $authenticationData->access_token);
		foreach ($stepsResponse as $step) {
			// Run triggers
			foreach ($step->triggers as $trigger) {
				$triggerURL = "http://".env('PM_SERVER')."/api/1.0/workflow/cases/{$case->APP_UID}/execute-trigger/{$trigger->tri_uid}";
				$triggerResponse = \Processmaker::executeREST($triggerURL, "PUT", [], $authenticationData->access_token);
			}
		}

		// Remove documents
		$documents = \App\VATUserApplicationDocument::where('APPLICATION_ID', $request->id)->get();

		foreach ($documents as $document) {
			$documentDeleteURL = "http://".env('PM_SERVER')."/api/1.0/workflow/cases/{$case->APP_UID}/1/input-document/{$document->DOCUMENT_UID}";
			$res = \Processmaker::executeREST($documentDeleteURL, "DELETE", [], $authenticationData->access_token, true);
			if($res){
				if ($res->error->code >= 400) {

				}
			}
			$document->delete();
		}

		$inputDocumentIDs = [];
		if ($inputDocument) {
			foreach ($documentPaths as $key => $path) {
				$inputData = [
					'inp_doc_uid'		=>	$inputDocument,
					'tas_uid'			=>	$process->task,
					'app_doc_comment'	=>	"Document uploaded by: " . \Auth::user()->name . " Focal Point " . \Auth::user()->focal_point->agency->ACRONYM . " through the external application",
					'form'            	=> new \CurlFile($path)
				];

				$inputDocumentRes = \Processmaker::executeREST($inputDocumentURL, "POST", $inputData, $authenticationData->access_token, true);
				$inputDocumentIDs[$key] = $inputDocumentRes->app_doc_uid;
			}
		}

		$response = \Processmaker::routeCase($case->APP_UID);

		if (empty($response)) {
			// $base_url = "http://".env('PM_SERVER_DOMAIN')."/sysworkflow/en/neoclassic/{$outputDocRes->app_doc_link}";

			$userApplication = \App\VATUserApplication::find($request->id);

			// $userApplication->CASE_NO = $case->APP_NUMBER;
			$userApplication->ACKNOWLEDGEMENT_LINK = '';
			$userApplication->USER_ID = \Auth::user()->id;
			$userApplication->STATUS = 'Pending';

			$userApplication->save();

			foreach ($documentPaths as $key => $path) {
				\App\VATUserApplicationDocument::create([
					'APPLICATION_ID'	=>	$userApplication->id,
					'PATH'				=>	$path,
					'DOCUMENT_UID'		=>	$inputDocumentIDs[$key]
				]);
			}

			\Mail::to($userApplication->user->focal_point->EMAIL)->send(new \App\Mail\AcknowledgeVATReceipt($userApplication));

			return [
				'case_no'			=>	$case->APP_NUMBER,
				// 'link'				=>	$base_url,
				// 'document_link'		=>	$outputDocRes->app_doc_link,
				'application_id'	=>	$userApplication->id
			];
		}else{
			return \Response::json($response, 500);
		}
	}

	function downloadAcknowledgement(Request $request){
		$application = \App\VATUserApplication::findOrFail($request->id);
		dd($application->data);
		// 
		// die();
		// $authenticationData = json_decode(\Storage::get("pmauthentication.json"));
		// setcookie("access_token",  $authenticationData->access_token,  $authenticationData->expiry);
		// setcookie("refresh_token", $authenticationData->refresh_token); //refresh token doesn't expire
		// setcookie("client_id",     env("PM_CLIENT_ID"));
		// setcookie("client_secret", env("PM_CLIENT_SECRET"));

		// dd($_COOKIE);
		// dd(strtotime('2019-10-02'));
		
		$link = $application->ACKNOWLEDGEMENT_LINK;

		$handle = fopen($link, "rb") or die("Error: Unable to open file:\n$link");
		$contents = stream_get_contents($handle) or die("Error: Unable to get file:\n$link");

		dd($contents);

		fclose($handle);

		//serve up the PDF file for download as attachment in user's browser:
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="Acknowledgement Receipt Case - '.$application->CASE_NO.'.pdf"');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . strlen($contents));
		print $contents;
		die;
	}

	function searchApplication(Request $request){
		$searchQueries = $request->get('query');
		$limit = $request->get('limit');
		$page = $request->get('page');
		$ascending = $request->get('ascending');
		$byColumn = $request->get('byColumn');
		$orderBy = $request->get('orderBy');

		$fields = [
			"Case No" 		=>	"CASE_NO"
		];

		// $queryBuilder = VATUserApplication::select('CASE_NO', 'created_at', 'STATUS');
		$queryBuilder = VATUserApplication::where('USER_ID', \Auth::id());
		// $queryBuilder = $queryBuilder->where('USER_ID', \Auth::id());
		// $queryBuilder = $queryBuilder->setAppends(['supplier']);
		if ($searchQueries) {
			$queryBuilder->where('CASE_NO', 'LIKE', "%{$searchQueries}%");
		}

		$count = $queryBuilder->count();

		$queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));
		if($orderBy)
			$queryBuilder = $queryBuilder->orderBy($fields[$orderBy], ($ascending == 1) ? 'ASC' : 'DESC');
		else
			$queryBuilder = $queryBuilder->orderBy('CASE_NO', 'DESC');


		$data = $queryBuilder->get();

		return [
			'data' 	=> $data,
			'count'	=>	$count
		];
	}

	function getVATApplication(Request $request){
		$id = $request->id;

		$application = VATUserApplication::where('id', $id)->where('USER_ID', \Auth::id())->with('documents')->firstOrFail();

		return $application;
	}
}
