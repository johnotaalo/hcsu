<?php

namespace App\Http\Controllers\FocalPoints;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VATController extends Controller
{
	function addVATApplication(Request $request){
		// $validatedData = $request->validate([

		// ]);

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
			$grid[$k] = [
				'documentType'			=>	$document['documentType'],
				'documentType_label'	=> ucwords($document['documentType']),
				'documentNo'			=>	$document['documentNo'],
				'documentDate'			=>	date('Y-m-d', strtotime($document['documentDate'])),
				'goodsServices'			=>	$document['goodsDescription'],
				'vatAmount'				=>	$document['vatAmount'],
				'etrNo'					=>	""
			];
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

		$stepsResponse = \Processmaker::executeREST($steps_url, "GET", [], $authenticationData->access_token);
		foreach ($stepsResponse as $step) {
			// Run triggers
			foreach ($step->triggers as $trigger) {
				$triggerURL = "http://".env('PM_SERVER')."/api/1.0/workflow/cases/{$app_uid}/execute-trigger/{$trigger->tri_uid}";
				$triggerResponse = \Processmaker::executeREST($triggerURL, "PUT", [], $authenticationData->access_token);
			}
		}

		// $response = \Processmaker::executeREST($outputDocumentURL, "POST", null, $authenticationData->access_token);
		// dd($variables_url);

		// $response = \Processmaker::executeREST($steps_url, "GET", [], $authenticationData->access_token);
		$outputDocRes = \Processmaker::executeREST($outputDocumentURL, "POST", [], $authenticationData->access_token);
		$response = \Processmaker::routeCase($app_uid);
		

		if ($outputDocRes) {
			$base_url = "http://processmakerdev.unon.org/sys" . env("PM_WORKSPACE") . "/en/neoclassic/{$outputDocRes->app_doc_link}";

			$userApplication = new \App\VATUserApplication();

			$userApplication->CASE_NO = $case_no;
			$userApplication->ACKNOWLEDGEMENT_LINK = $base_url;
			$userApplication->USER_ID = \Auth::user()->id;

			$userApplication->save();
			return [
				'case_no'			=>	$case_no,
				'link'				=>	$base_url,
				'application_id'	=>	$userApplication->id
			];
		}
	}
}
