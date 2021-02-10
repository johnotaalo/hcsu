<?php

namespace App\Http\Controllers\FocalPoints;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplicationsController extends Controller
{
    function get(Request $request){
    	$searchQueries = $request->get('normalSearch');
        $statusQuery = $request->get('statusSearch');
        $processQuery = $request->get('processSearch');
        $limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $queryBuilder = \App\Models\UserApplications::select('*')->where('AUTHENTICATION_SOURCE', 'USER');
        // ->with('process')
        /* */

        if (\Auth::user()->type == "1") {
            $queryBuilder->where('SUBMITTED_BY', \Auth::user()->id);
        }

        $columnMap = [
            'CASE_NO'           =>  'APP_NUMBER', 
            'PROCESS_NAME'      =>  'PRO_TITLE',
            'CREATED'           =>  'CREATED_AT'       
        ];

        if ($searchQueries) {
        	$queryBuilder->where('PROCESS_UID', 'LIKE', "%{$searchQueries}%");
        }

        if($statusQuery){
            $queryBuilder->where('STATUS', $statusQuery);
        }

        if ($processQuery) {
            $queryBuilder->where('PROCESS_UID', $processQuery);
        }

        if ($orderBy) {
            $column = $columnMap[$orderBy];

            if ($column != "PRO_TITLE") {
                $queryBuilder->orderBy($column, ($ascending == 1) ? 'ASC' : 'DESC');
            }
        }

        $count = $queryBuilder->count();
        $queryBuilder = $queryBuilder->with('caseDetails')->with('assigned')->limit($limit)->skip($limit * ($page - 1));

        $data = $queryBuilder->get();

        if ($orderBy && $columnMap[$orderBy] == "PRO_TITLE") {
            if ($ascending == 1) {
                $data = ($data->sortBy('process.PRO_TITLE'))->values()->all();
            }else{
                $data = ($data->sortByDesc('process.PRO_TITLE'))->values()->all();
            }
        }

    	return [
    		'count'	=>	$count,
    		'data'	=>	$data
    	];
    }

    function getAllApplications(){
        $data = \App\Models\UserApplications::where('AUTHENTICATION_SOURCE', 'USER')->where('SUBMITTED_BY', \Auth::user()->id)->get();

        return $data;
    }

    function getUsers(){
        $users = \App\Models\PM\User::where('USR_STATUS', 'ACTIVE')->get();

        return $users;
    }

    function getApplication(Request $request){
        $id = $request->id;

        $data = \App\Models\UserApplications::with('files')->findOrFail($id);

        return $data;
    }

    function cancelApplication(Request $request){
        $id = $request->id;

        $application = \App\Models\UserApplications::findOrFail($id);

        $application->STATUS = "CANCELED";

        $application->save();

        return $application;
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
    		'PROCESS_UID'			=>	$input['process']['PRO_UID'],
    		'HOST_COUNTRY_ID'		=>	$input['client']['HOST_COUNTRY_ID'],
    		'COMMENT'				=>	$input['comment'],
    		'SUBMITTED_BY'			=>	\Auth::user()->id,
    		'AUTHENTICATION_SOURCE'	=>	'USER'
    	]);

        foreach ($input['uploads'] as $key => $upload) {
            $file = new \App\Model\UserApplicationFile();

            $file->USER_APPLICATION_ID = $application->id;
            $file->FILE_DESCRIPTION = $upload['description'];
            $file->FILE_URL = $this->uploadFile($request->file('uploads')[$key]['file']);

            $file->save();
        }

        return $application;
    }

    function edit(Request $request){
        $input = $request->input();

        $application = \App\Models\UserApplications::findOrFail($request->id);

        $application->PROCESS_UID = $input['process']['PRO_UID'];
        $application->HOST_COUNTRY_ID = $input['client']['HOST_COUNTRY_ID'];
        $application->COMMENT = $input['comment'];
        $application->STATUS = "SUBMITTED";

        $application->save();

        $documents = [];

        foreach ($input['uploads'] as $key => $upload) {
            if ($request->hasFile("uploads.{$key}.file")) {
                $filePath = $this->uploadFile($request->file('uploads')[$key]['file']);
            }else if(!isset($upload['file']) && $upload['edit']){
                $file = \App\Model\UserApplicationFile::find($upload['id']);
                if ($file) {
                    $filePath = $file->FILE_URL;
                }
            }

            $documents[] = [
                'USER_APPLICATION_ID'   =>  $application->id,
                'FILE_DESCRIPTION'      =>  $upload['description'],
                'FILE_URL'              =>  $filePath
            ];
        }

        \App\Model\UserApplicationFile::where('USER_APPLICATION_ID', $application->id)->delete();

        foreach ($documents as $document) {
            $file = new \App\Model\UserApplicationFile();

            $file->USER_APPLICATION_ID = $application->id;
            $file->FILE_DESCRIPTION = $document['FILE_DESCRIPTION'];
            $file->FILE_URL = $document['FILE_URL'];

            $file->save();
        }

        return $application;
    }

    function assign(Request $request){
        $application = \App\Models\UserApplications::find($request->id);
        // dd($request->input('submitApplication'));
        if ($request->input('submitApplication') == 'true') {
            $authenticationData = json_decode(\Storage::get("pmauthentication.json"));
            $startTask = \DB::connection('pm')->table('TASK')->where('PRO_UID', $application->PROCESS_UID)->where('TAS_TITLE', 'LIKE', 'Receive%')->first();
            if ($startTask) {
                if (\App::environment('local') || \App::environment('staging')) {
                    $url = "http://".env('PM_SERVER')."/api/1.0/workflow/cases/impersonate";
                }else{
                    $url = "https://".env('PM_SERVER_DOMAIN')."/api/1.0/workflow/cases/impersonate";
                }

                $clientType = identify_hcsu_client($request->input('host_country_id'));
                $clientObj = new \StdClass;

                $host_country_id = $request->input('host_country_id');

                if ($clientType == "staff") {
                    $principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $host_country_id)->first();
                    $contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$host_country_id})"))->first();

                    $mission = $contract->ACRONYM;
                    $name = strtoupper($principal->LAST_NAME . ", " . format_other_names($principal->OTHER_NAMES));
                    $client_name = $name;

                    $clientObj->name = $client_name;
                    $clientObj->organization = $mission;
                }
                else if($clientType == "agency"){
                    $agency = \App\Models\Agency::where('HOST_COUNTRY_ID', $host_country_id)->first();
                    $clientObj->name = $agency->ACRONYM;
                    $clientObj->organization = $agency->ACRONYM;
                }else if($clientType == "dependent"){
                    $dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $host_country_id)->first();
                    $contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$dependent->PRINCIPAL_ID})"))->first();

                    $mission = $contract->ACRONYM;
                    
                    $relationship = $dependent->relationship->RELATIONSHIP;
                    $relationship = ($relationship == "Spouse") ? strtolower($relationship) : "dependent";

                    $name = strtoupper($dependent->LAST_NAME . ", " . format_other_names($dependent->OTHER_NAMES));
                    $client_name = $name;

                    $clientObj->name = "{$client_name} {$dependent->relationship->RELATIONSHIP} of {$dependent->principal->fullname}";
                    $clientObj->organization = $mission;
                }

                $caseData =[[
                    'host_country_id'           =>  $request->input('host_country_id'),
                    'host_country_id_label'     =>  $request->input('host_country_id'),
                    'client_name'               =>  $clientObj->name,
                    'clientOrganization'        =>  $clientObj->organization,
                ]];

                $data = [
                    "pro_uid"   =>  $application->PROCESS_UID,
                    "tas_uid"   =>  $startTask->TAS_UID,
                    "usr_uid"   =>  $request->input('assignedTo')['USR_UID'],
                    "variables" =>  $caseData
                ];

                $response = \Processmaker::executeREST($url, "POST", $data, $authenticationData->access_token);
                
                $case_no = $response->app_number;
                $app_uid = $response->app_uid;

                if (\App::environment('local') || \App::environment('staging')) {
                    $statusURL = "http://".env('PM_SERVER')."/api/1.0/workflow/extrarest/case/status/{$app_uid}";
                }else{
                    $statusURL = "https://".env('PM_SERVER_DOMAIN')."/api/1.0/workflow/extrarest/case/status/{$app_uid}";
                }

                $aVars = [
                    'status' => 'TO_DO'
                ];

                $res = \Processmaker::executeREST($statusURL, "PUT", $aVars, $authenticationData->access_token);

                // $setVariablesURL = "http://".env('PM_SERVER')."/api/1.0/workflow/cases/{$app_uid}/variable";
                // $response = \Processmaker::executeREST($setVariablesURL, "PUT", $caseData, $authenticationData->access_token);

                $application->ASSIGNED_TO = $request->input('assignedTo')['USR_UID'];
                $application->APP_UID = $app_uid;
                $application->APP_NUMBER = $case_no;
                $application->STATUS = "ASSIGNED";
                $application->CURRENT_USER = "ADMINISTRATIVE_ASSISTANT";
                $application->SUPERVISOR_COMMENTS = $request->input('supervisorComments');

                $application->save();

                // dd($res);
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
