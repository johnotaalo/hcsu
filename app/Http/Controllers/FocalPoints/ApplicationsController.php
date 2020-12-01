<?php

namespace App\Http\Controllers\FocalPoints;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplicationsController extends Controller
{
    function get(Request $request){
    	$searchQueries = $request->get('normalSearch');
        $limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $queryBuilder = \App\Models\UserApplications::select('*');
        // ->with('process')
        /* */

        if ($searchQueries) {
        	$queryBuilder->where('PROCESS_UID', 'LIKE', "%{$searchQueries}%");
        }

        $count = $queryBuilder->count();
        $queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));

        $data = $queryBuilder->get();
    	return [
    		'count'	=>	$count,
    		'data'	=>	$data
    	];
    }

    function getUsers(){
        $users = \App\Models\PM\User::where('USR_STATUS', 'ACTIVE')->get();

        return $users;
    }

    function getApplication(Request $request){
        $id = $request->id;

        $data = \App\Models\UserApplications::findOrFail($id);

        return $data;
    }

    function new(Request $request){
    	$input = $request->input();
    	// dd($request->hasFile('uploads'));
    	if ($request->input('process') == null) {
    		$input["process"] =  [
    			"prj_uid" => "1107820965eb2b13bc0c284023169236"
    		];
    	}

    	$application = \App\Models\UserApplications::create([
    		'PROCESS_UID'			=>	$input['process']['prj_uid'],
    		'HOST_COUNTRY_ID'		=>	'10001000',
    		'COMMENT'				=>	$input['comment'],
    		'SUBMITTED_BY'			=>	\Auth::user()->id,
    		'AUTHENTICATION_SOURCE'	=>	'USER'
    	]);


    	$file = new \App\Model\UserApplicationFile();

    	$file->USER_APPLICATION_ID = $application->id;
    	$file->FILE_URL = $this->uploadFile($request->file('uploads'));

    	$file->save();
    }

    function assign(Request $request){
        $application = \App\Models\UserApplications::find($request->id);
        // dd($request->input('submitApplication'));
        if ($request->input('submitApplication') == 'true') {
            $authenticationData = json_decode(\Storage::get("pmauthentication.json"));
            $startTask = \DB::connection('pm')->table('TASK')->where('PRO_UID', $application->PROCESS_UID)->where('TAS_TITLE', 'LIKE', 'Receive%')->first();
            if ($startTask) {
                if (\App::environment('local') || \App::environment('staging')) {
                    $url = "http://".env('PM_SERVER')."/api/1.0/workflow/cases";
                }else{
                    $url = "https://".env('PM_SERVER_DOMAIN')."/api/1.0/workflow/cases";
                }

                $caseData =[
                    'host_country_id'           =>  $request->input('HOST_COUNTRY_ID'),
                    'host_country_id_label'     =>  $request->input('HOST_COUNTRY_ID')
                ];

                $data = [
                    "pro_uid"   =>  $application->PROCESS_UID,
                    "tas_uid"   =>  $startTask->TAS_UID,
                    "variables" =>  $caseData
                ];

                $response = \Processmaker::executeREST($url, "POST", $data, $authenticationData->access_token);
                
                $case_no = $response->app_number;
                $app_uid = $response->app_uid;

                // $setVariablesURL = "http://".env('PM_SERVER')."/api/1.0/workflow/cases/{$app_uid}/variable";
                // $response = \Processmaker::executeREST($setVariablesURL, "PUT", $caseData, $authenticationData->access_token);

                $application->ASSIGNED_TO = $request->input('assignedTo')['USR_UID'];
                $application->APP_UID = $app_uid;
                $application->APP_NUMBER = $case_no;
                $application->STATUS = "ASSIGNED";
                $application->CURRENT_USER = "ADMINISTRATIVE_ASSISTANT";
                $application->SUPERVISOR_COMMENTS = $request->input('supervisorComments');

                $application->save();
            }
        }else{
            $application->STATUS = "QUERIED";
            $application->CURRENT_USER = "SELF";
            $application->SUPERVISOR_COMMENTS = $request->input('supervisorComments');

            $application->save();
        }
        
       
        // $authenticationData = json_decode(\Storage::get("pmauthentication.json"));
        // 
        // $processes = \Processmaker::executeREST($url, "GET", null, $authenticationData->access_token);
       

        

        // $application->ASSIGNED_TO = "Chrispine Otaalo [Test]";
        // $application->CURRENT_USER = "ADMINISTRATIVE_ASSISTANT";
        // $application->STATUS = "ASSIGNED";
        // $application->save();

        return $application;
    }

    function uploadFile($file){
    	$path = $file->store('user_applications');

    	return $path;
    }
}
