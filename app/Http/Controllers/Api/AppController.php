<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Agency;
use App\Models\PassportType;
use App\Models\Country;
use App\Models\ContractType;
use App\Models\Grade;
use App\Models\Relationship;
use App\Models\ContractDesignation;
use App\FormTemplate;

use App\Helpers\HCSU\AdobeSign\AdobeClient;
use App\Helpers\HCSU\ProcessMaker;

use Storage;
use mikehaertl\pdftk\Pdf;

use App\Enums\UserType;

use App;

class AppController extends Controller
{
    function index(Request $request){
        // dd($request->query());
        if(\Auth::check()){
            $userType = \Auth::user()->user_type;

            if ($userType == UserType::getInstance(UserType::FocalPoint) || is_null($userType) || $userType=="") {
                return redirect()->route('clients-home');
            }
        }
        $data = [];
        $data['iframe'] = false;
        $data['case_no'] = "";
        $data['user'] = $request->query('user');
        // dd($request->query());
        // echo "<pre>";print_r($request->url());die;
        if ($request->query('type') == "iframe") {
            $data['iframe'] = true;
        }

        if($request->query('case_no') != ""){
            $data['case_no'] = $request->query('case_no');
        }

        // dd($data);

    	return view('app.main')->with($data);
    }

    function searchClient(Request $request){
        $identity = $request->query('id');
//         $data = \DB::connection('pm_data')->select("SELECT
//     `PC`.`PRINCIPAL_ID` AS `PRINCIPAL_ID`,
//     `P`.`LAST_NAME` AS `LAST_NAME`,
//     `P`.`OTHER_NAMES` AS `OTHER_NAMES`,
//     `A`.`ACRONYM` AS `ACRONYM`,
//     `A`.`AGENCYNAME` AS `AGENCYNAME`,
//     `PCR`.`START_DATE` AS `START_DATE`,
//     `PC`.`DESIGNATION` AS `DESIGNATION`,
//     `PC`.`GRADE` AS `GRADE`,
//     `PC`.`INDEX_NO` AS `INDEX_NO`,
//     max( `PCR`.`END_DATE` ) AS `END_DATE`
// FROM
//     (((
//                 `pm_master_data`.`PRINCIPAL_CONTRACT` `PC`
//                 JOIN `pm_ref_data`.`agencies` `A` ON ((
//                         `A`.`AGENCY_ID` = `PC`.`AGENCY_ID`
//                     )))
//             JOIN `pm_master_data`.`PRINCIPAL_CONTRACT_RENEWALS` `PCR` ON ((
//                     `PCR`.`CONTRACT_ID` = `PC`.`ID`
//                 )))
//         JOIN `pm_master_data`.`PRINCIPAL` `P` ON ((
//                 `P`.`HOST_COUNTRY_ID` = `PC`.`PRINCIPAL_ID`
//             )))
//             WHERE (PC.PRINCIPAL_ID = '{$identity}' OR PC.INDEX_NO = '{$identity}') AND P.STATUS = 1
// GROUP BY
//     `PC`.`PRINCIPAL_ID`,
//     `PC`.`ID`,
//     P.HOST_COUNTRY_ID,
//     `P`.`LAST_NAME`,
//     `P`.`OTHER_NAMES`,
//     `A`.`ACRONYM`,
// `A`.`AGENCYNAME`,
// `PCR`.`START_DATE`,
// `PC`.`DESIGNATION`,
// `PC`.`GRADE`,
// `PC`.`INDEX_NO`
// ORDER BY
//     `END_DATE` DESC
//     LIMIT 1");

         $data = \DB::connection('pm_data')->select("SELECT PRINCIPAL_ID, LAST_NAME, OTHER_NAMES, ACRONYM, AGENCYNAME, START_DATE, DESIGNATION, GRADE, INDEX_NO, END_DATE FROM VW_STAFF_LATEST_CONTRACT WHERE PRINCIPAL_ID = '{$identity}' OR INDEX_NO = '{$identity}'");
        if (count($data)) {
            return (array)$data[0];
        }

        abort(404);
    }

    function validateSession(Request $request){
        // dd("Session data is here");
        $session = $request->session_id;
        // $session = "KRbhNqovZo5ZSdi9iUX0ITCBqpOtaLt1zTf8ryrV";

        $mysession = session()->getId();
        dd(\Hash::check($session, $mysession));
        dd("{$session} => {$mysession}");


        $sessionData = \DB::table('sessions')->where('id', $session)->where('ip_address', $request->ip_address)->where('user_agent', $request->user_agent)->first();

        $payload = unserialize(base64_decode($sessionData->payload));

        dd($payload);
    }

    function getAgencies(){
    	return Agency::where('IS_ACTIVE', true)->get();
    }

    function getPassportTypes(){
    	return PassportType::all();
    }

    function getCountries(){
    	return Country::all();
    }

    function getContractTypes(){
    	return ContractType::all();
    }

    function getGrades(){
    	return Grade::orderBy('order', 'ASC')->get();
    }

    function getRelationships(){
    	return Relationship::all();
    }

    function getDesignations(){
        return ContractDesignation::all();
    }

    function getPrincipalOptions(){
    	$response = [];

    	$response['countries'] = $this->getCountries();
    	$response['agencies'] = $this->getAgencies();
    	$response['passportTypes'] = $this->getPassportTypes();
    	$response['contractTypes'] = $this->getContractTypes();
    	$response['grades'] = $this->getGrades();
    	$response['relationships'] = $this->getRelationships();
        $response['designations'] = $this->getDesignations();

    	return $response;
    }

    function updateHostCountryID(Request $request){
        $case = $request->case_no;
        $application = $request->application;
        $host_country_id = $request->host_country_id;
        $clientType = identify_hcsu_client($host_country_id);

        if ($application == 'pin') {
            switch ($clientType) {
                case 'agency':
                    $agency = \App\Models\Agency::where('HOST_COUNTRY_ID', $host_country_id)->first();
                    $agency->PIN_NO = null;
                    $agency->save();
                    break;
                case 'staff':
                    $staff = \App\Models\Principal::where('HOST_COUNTRY_ID', $host_country_id)->first();
                    $staff->PIN_NO = null;
                    $staff->save();
                    break;
                case 'dependent':
                    $dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $host_country_id)->first();
                    $dependent->PIN = null;
                    $dependent->save();
                    break;
                default:
                    # code...
                    break;
            }
        }

        $del_index = 1;
        $variable_name = "host_country_id";

        if (App::environment('local') || App::environment('staging')) {
            $url = "http://".env('PM_SERVER')."/api/1.0/workflow/variable/{$case}/{$del_index}/variable/{$variable_name}";
        }else{
            $url = "https://".env('PM_SERVER_DOMAIN')."/api/1.0/workflow/variable/{$case}/{$del_index}/variable/{$variable_name}";
        }

        $data = [
            $variable_name => $host_country_id,
            $variable_name . '_label' => $host_country_id
        ];

        if ($clientType == "agency"){
            $email = "";
            $agency = \App\Models\Agency::where('HOST_COUNTRY_ID', $host_country_id)->with("focalPointMapping")->first();
//            dd($agency->focal_point_mapping);
            if ($agency->EMAIL){
                $email = $agency->EMAIL;
            }else if ($agency->focal_point_mapping != null && count($agency->focal_point_mapping)){
                $focalpoints = $agency->focal_point_mapping;

                $emailsArray = $focalpoints->map(function($focalpoint){
                    return $focalpoint->EMAIL;
                });

                dd($emailsArray);
            }
            $data["clientEmail"] = "";
        }

        $authenticationData = json_decode(Storage::get("pmauthentication.json"));

        $response = \Processmaker::executeREST($url, "PUT", $data, $authenticationData->access_token);

        // dd($response);

        return $response;
    }

    function getCaseVariable(Request $request){
        $case = $request->case_no;
        $del_index = 1;
        $variable_name = $request->variable;

        if (App::environment('local') || App::environment('staging')) {
            $url = "http://".env('PM_SERVER')."/api/1.0/workflow/variable/{$case}/{$del_index}/variable/{$variable_name}";
        }else{
            $url = "https://".env('PM_SERVER_DOMAIN')."/api/1.0/workflow/variable/{$case}/{$del_index}/variable/{$variable_name}";
        }

        $response = \Processmaker::executeREST($url, "GET", [], (new \Processmaker())->authData()->access_token);
        // dd($response);
        $caseVariable = get_object_vars($response);
        return $caseVariable;
    }

    function addTemplate(Request $request){
        // dd($request);
        $validatedData = $request->validate([
            'name' =>  'required',
            'process'   =>  'required',
            'task'      =>  'required',
            'file'      =>  'required|mimes:pdf'
        ]);
        $process = $request->input('process');
        $task = $request->input('task');
        $step = $request->input('step');
        $template = $request->file('file')->store("templates/{$process['prj_uid']}/{$task['act_uid']}");
        $path = storage_path('app/'. $template);

        $template = FormTemplate::create([
            "form_name"             => $request->input('name'),
            "input_document"        => $request->input('input_document'),
            "process"               => $process['prj_uid'],
            "task"                  => $task['act_uid'],
            "step"                  =>  $step['step_uid'],
            "path"                  => $template,
            "type"                  =>  $request->input('type'),
            "ADOBE_SIGN_TEMPLATE"   =>  $request->input('adobe_sign_template')['value']
        ]);

        $config = [];

        if (env('PDFTK_COMMAND')) {
            $config = [
                'command'   =>  env('PDFTK_COMMAND'),
                'useExec'   =>  true
            ];
        }

        $pdf = new Pdf($path, $config);
        $data = $pdf->getDataFields();
        $fileName = str_replace(" ", "", $request->input('name')) ;
        Storage::disk('local')->put("templates/{$process['prj_uid']}/{$task['act_uid']}/{$fileName}.json", json_encode($data));

        return ['data' => $data];
    }

    function editTemplate(Request $request){
        $validatedData = $request->validate([
            'name'      =>  'required',
            'process'   =>  'required',
            'task'      =>  'required'
        ]);

        $process = $request->input('process');
        $task = $request->input('task');
        $step = $request->input('step');

        if ($request->has('file') && !is_null($request->file('file'))) {
            $template = $request->file('file')->store("templates/{$process['prj_uid']}/{$task['act_uid']}");
            $path = storage_path('app/'. $template);
        }else{
            $template = FormTemplate::find($request->id)->path;
        }

        $template = FormTemplate::where('id', $request->id)->update([
            "form_name"             => $request->input('name'),
            "input_document"        => $request->input('input_document'),
            "process"               => $process['prj_uid'],
            "task"                  => $task['act_uid'],
            "step"                  =>  $step['step_uid'],
            "path"                  =>  $template,
            "type"                  =>  ($request->input('type') == "null") ? null : $request->input('type'),
            "ADOBE_SIGN_TEMPLATE"   =>  $request->input('adobe_sign_template')['value']
        ]);

        $fileName = str_replace(" ", "", $request->input('name')) ;

        if($request->file('file') != NULL){
            if (env('PDFTK_COMMAND')) {
                $config = [
                    'command'   =>  env('PDFTK_COMMAND'),
                    'useExec'   =>  true
                ];
            }

            $pdf = new Pdf($path, $config);
            $data = $pdf->getDataFields();
            Storage::disk('local')->put("templates/{$process['prj_uid']}/{$task['act_uid']}/{$fileName}.json", json_encode($data));

            return ['data' => $data];
        }

        return ['data' => json_decode(Storage::disk('local')->get("templates/{$process['prj_uid']}/{$task['act_uid']}/{$fileName}.json"))];
    }

    function getTemplate(Request $request){
        $id = $request->id;

        $template = FormTemplate::findOrFail($id);

        return $template;
    }

    function getTemplateList(Request $request){
        $searchQueries = $request->get('normalSearch');
        $limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $query = FormTemplate::with('process');

        $data = $query->get();
        $count = count($data);

        return [
            'data'  =>  $data,
            'count' =>  $count
        ];
    }

    function getNVDataFields(){
        $config = [];

        if (env('PDFTK_COMMAND')) {
            $config = [
                'command'   =>  env('PDFTK_COMMAND'),
                'useExec'   =>  true
            ];
        }

        $template = public_path('templates/NV.pdf');
        $pdf = new Pdf($template, $config);
        $data = $pdf->getDataFields();
        Storage::disk('local')->put("templates/note-verbal.json", json_encode($data));

        die(json_encode($data));
    }

    function getLocalProcessList(){
        $processes = \App\Models\PM\Process::all();

        return $processes;
    }

    function getProcessList(){
        if (App::environment('local') || App::environment('staging')) {
            $url = "http://".env('PM_SERVER')."/api/1.0/workflow/project";
        }else{
            $url = "https://".env('PM_SERVER_DOMAIN')."/api/1.0/workflow/project";
        }

        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "GET", NULL, $authenticationData->access_token);

        return $response;
    }

    function getProcessTasks(Request $request){
        if (App::environment('local') || App::environment('staging')) {
            $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/project/" . $request->process;
        }else{
            $url = "https://" . env("PM_SERVER_DOMAIN") . "/api/1.0/" . env("PM_WORKSPACE") . "/project/" . $request->process;
        }
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "GET", NULL, $authenticationData->access_token);

        return $response->diagrams[0]->activities;
    }

    function getTaskSteps(Request $request){
        if (App::environment('local') || App::environment('staging')) {
            $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/project/" . $request->process . "/activity/{$request->task}/steps";
        }else{
            $url = "https://" . env("PM_SERVER_DOMAIN") . "/api/1.0/" . env("PM_WORKSPACE") . "/project/" . $request->process . "/activity/{$request->task}/steps";
        }

        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "GET", NULL, $authenticationData->access_token);

        return $response;
    }

    function generateNOA(Request $request){
        $document = FormTemplate::where([
            'form_name' =>  'NOA'
        ])->first();

        if($document){
            $path = storage_path('app/'. $document->path);
            $form = new \App\Helpers\HCSU\PDFTK\NOA();
            $config = [];
            if (env('PDFTK_COMMAND')) {
                $config = [
                    'command'   =>  env('PDFTK_COMMAND'),
                    'useExec'   =>  true
                ];
            }

            $pdf = new Pdf($path, $config);
            $pdf->fillForm($form->formData($request->host_country_id))
                ->flatten()
                ->execute();

            $content = file_get_contents($pdf->getTmpFile());
            $filename = $form->getFileName();
            $localFile = "forms/NOA/{$filename}.pdf";
            \Storage::put($localFile, $content);
            // Upload to processmaker
            // return \Storage::download($localFile);
            return response()->file(storage_path("app/{$localFile}"));
        }
    }

    function generateDocument(Request $request){
        \Log::debug("Generate Document. Input Sent: " . json_encode($request->input()));
        $case = $this->getCaseInformation($request->case_no);
        $applicationType = ($request->query('type')) ? $request->query('type') : "";
        // $variables = $this->getCaseVariables($request->case_no);
        $extraParams = $request->query();

        // dd($case);
        \Log::debug("Case Information: " . json_encode($case));
        $process = $case->pro_uid;
        $currentTask = $case->current_task[0]->tas_uid;

        if(!$applicationType){
            \Log::debug("No application type information");
            $document = FormTemplate::where([
                'process'   =>  $process,
                'task'      =>  $currentTask
            ])->first();
        }else{
            \Log::debug("Using application type information");
            $document = FormTemplate::where([
                'process'   =>  $process,
                // 'task'      =>  $currentTask,
                'type'      =>  $applicationType
            ])->first();
        }

        \Log::debug("Document Information: " . json_encode($document));

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
            $filename = $class->getFileName();
            // $pdf->fillForm($data)
            //     ->needAppearances()
            //     ->saveAs("{$filename}.pdf");
            // $pdf->send("{$filename}", true);
            try{
                $pdf->fillForm($data)
                    ->flatten()
                    ->execute();
               if($pdf->getError()){
                abort(500, $pdf->getError());
               }
                $content = file_get_contents($pdf->getTmpFile());
                $localFile = "forms/{$process}/{$filename}-{$case->app_number}.pdf";
                \Storage::put($localFile, $content);

                $stampedFile = "forms/{$process}/{$filename}-{$case->app_number}.pdf";

                // $stampPdf = new Pdf(storage_path('app/' . $localFile), $config);
                // $stampPdf->stamp(public_path() . '/documents/stamps/olago-edit.pdf')
                //             ->saveAs(storage_path("app/{$stampedFile}"));
                // $documentId = \App\Helpers\HCSU\AdobeSign\AdobeClient::uploadDocument($localFile, $filename);
                // \Log::info("Document ID: {$documentId}");
                // $agreementId = \App\Helpers\HCSU\AdobeSign\AdobeClient::sendDocumentForSigning($documentId, $case->app_name);

                // Upload to processmaker
                if ($document->input_document != null) {
                    $this->uploadGeneratedForm($case->app_uid, $currentTask, $document, $localFile);
                }
                // return \Storage::download($localFile);
                if(isset($request->download) && $request->download == 1){
                    return \Storage::download($localFile);
                }
                return response()->file(storage_path("app/{$stampedFile}"));
            }catch(\Exception $ex){
                dd($ex);
            }
        }
        else{
            abort(404);
        }
    }

    function generateNoteVerbal(Request $request){
        $downloadType = ($request->query('download')) ? (bool)$request->query('download') : false;
        $nvOnly = ($request->query('type') == "nv") ? true : false;
        $path = public_path();

        $case = $this->getCaseInformation($request->case_no);
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

        $data = new \StdClass;

        switch($request->process){
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
            case 'airport-pass':
                $data = \App\Helpers\HCSU\Data\AirportPassData::get($case->app_number);
                break;
            case 'airport-pass-locals':
                $data = \App\Helpers\HCSU\Data\AirportPassLocalsData::get($case->app_number);
                break;
        }

        // dd($data);

        $noteVerbal = new \App\Helpers\HCSU\PDFTK\NoteVerbal($request->process, $data, $initials);

        $config = [];
        if (env('PDFTK_COMMAND')) {
            $config = [
                'command'   =>  env('PDFTK_COMMAND'),
                'useExec'   =>  true
            ];
        }
        try{
        if ($request->process == "logbook") {
            $pdf = new Pdf(public_path('templates/General_Note_Verbal_Ntsa.pdf'), $config);
        }
        else{
            $pdf = new Pdf(public_path('templates/NV.pdf'), $config);
        }
        $pdf->fillForm(['main_body' => $noteVerbal->getContent()])
                ->flatten()
                ->execute();


        $content = file_get_contents($pdf->getTmpFile());
        $localFile = "note-verbals/{$request->process}/Note Verbal - {$case->app_number}.pdf";
        \Storage::put($localFile, $content);

        $case = $this->getCaseInformation($request->case_no);
        $process = $case->pro_uid;
        $currentTask = $case->current_task[0]->tas_uid;
        $search = [
            'process'   =>  $process,
            'task'      =>  $currentTask
        ];

        if ($nvOnly) {
            $search['type'] = "nv";
        }

        $document = FormTemplate::where($search)->first();

        if($document){
            if ($document->input_document != null) {
                $res = $this->uploadGeneratedForm($case->app_uid, $currentTask, $document, $localFile);
                \Log::debug("ProcessMaker upload documents: " . json_encode($res));
            }
        }

        if($downloadType){
            return \Storage::download($localFile);
        }
        return response()->file(storage_path("app/{$localFile}"));
    }catch(\Exception $ex){
        dd($ex);
    }
    }

    function uploadGeneratedForm($case_no, $task_id, $document, $localFile){
        $inputDocuments = $this->getGeneratedDocuments($case_no);
        // // dd($inputDocuments);
        // if (count($inputDocuments)) {
        //     foreach ($inputDocuments as $inputDocument) {
        //         $this->deleteDocument($case_no, $inputDocument->app_doc_uid, $inputDocument->app_doc_index);
        //     }
        // }
        if (App::environment('local') || App::environment('staging')) {
            $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no . "/input-document";
        }else{
            $url = "https://" . env("PM_SERVER_DOMAIN") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no . "/input-document";
        }

        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $form = storage_path('app/'. $localFile);
        $fx = new \CurlFile( $form );
        // dd($fx);
        // $form = Storage::get($localFile);
        $data = [
            'inp_doc_uid'       =>  $document->input_document,
            'tas_uid'           =>  $task_id,
            'app_doc_comment'   =>  "Document generated and uploaded on: " . date('F d, Y H:i'),
            'form'              =>  new \CurlFile( $form )
        ];

        $response = \Processmaker::executeREST($url, "POST", $data, $authenticationData->access_token, true);

        return $response;
    }

    function getGeneratedDocuments($case_no){
        if (App::environment('local') || App::environment('staging')) {
            $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no . "/input-documents";
        }else{
            $url = "https://" . env("PM_SERVER_DOMAIN") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no . "/input-documents";
        }

        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "GET", NULL, $authenticationData->access_token);

        return $response;
    }

    function deleteDocument($case_no, $doc_id, $index){
        if (App::environment('local') || App::environment('staging')) {
            $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no . "/2/input-document/{$doc_id}";
        }else{
            $url = "https://" . env("PM_SERVER_DOMAIN") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no . "/2/input-document/{$doc_id}";
        }
        // dd($url);
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "DELETE", NULL, $authenticationData->access_token, true);

        // dd($response);

        return $response;
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

    function getCaseInformation($case_no){
        if (App::environment('local') || App::environment('staging')) {
            $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no;
        }else{
            $url = "https://" . env("PM_SERVER_DOMAIN") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no;
        }

        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "GET", NULL, $authenticationData->access_token);
        // dd($response);

        return $response;
    }

    function getCaseVariables($case_no){
        if (App::environment('local') || App::environment('staging')) {
            $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no . '/variables';
        }else{
            $url = "https://" . env("PM_SERVER_DOMAIN") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no . '/variables';
        }
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "GET", NULL, $authenticationData->access_token);

        return $response;
    }

    function downloadVATData(){
        $vat_data = \App\Model\VAT::all();

        return $vat_data;
    }

    function getUsers(){
        $users = \App\Models\PM\User::where('USR_STATUS', 'ACTIVE')->get();

        return $users;
    }

    function ipmisFunctionality(){
        $processes = \DB::connection('pm')->table('PROCESS')->where('PRO_STATUS', 'ACTIVE')->select('PRO_UID', 'PRO_TITLE')->get();
        // dd($processes);
        foreach ($processes as $process) {
            $data = \App\Models\IPMISFunctionality::firstOrCreate(['PROCESS_UID' => $process->PRO_UID, 'PROCESS_NAME' => $process->PRO_TITLE]);
        }

        $ipmis_functional = \App\Models\IPMISFunctionality::all();
        return $ipmis_functional;
    }

    function toggleIPMISFunctionality(Request $request){
        $functionality = \App\Models\IPMISFunctionality::find($request->id);

        $functionality->IPMIS_FUNCTIONAL = ($request->functionality == "true") ? 1 : 0;

        $updated = $functionality->save();

        if($updated){
            $connector = ($request->functionality == "true") ? "activated" : "deactivated";
            return ['message' => "Successfully {$connector} IPMIS Functionality for {$functionality->PROCESS_NAME}"];
        }
    }

    function ipmisSubprocesses(Request $request){
        $filterSearch = json_decode($request->filterSearch);
        $limit = $request->get('limit');
        $page = $request->get('page');

        // $applications = \App\Models\PM\SubApplication::all();
        $queryBuilder = \DB::connection('pm')->table('SUB_APPLICATION')->select(\DB::raw('parent.APP_NUMBER as parent_case, app.APP_NUMBER as subprocess_case, parent.APP_TITLE as case_title, content.CON_VALUE as process, concat(user.USR_LASTNAME, ", ", user.USR_FIRSTNAME) as creator'));
        $queryBuilder = $queryBuilder->join('APPLICATION AS parent', 'SUB_APPLICATION.APP_PARENT', '=', 'parent.APP_UID')
                        ->join('APPLICATION AS app', 'SUB_APPLICATION.APP_UID', '=', 'app.APP_UID')
                        ->join('USERS AS user', 'user.USR_UID', '=', 'parent.APP_INIT_USER')
                        ->join('CONTENT AS content', 'parent.PRO_UID', '=', 'content.CON_ID')
                        ->where('content.CON_CATEGORY', "PRO_TITLE");

        if($filterSearch){
            $process = $filterSearch->process;
            $creator = $filterSearch->creator;

            if($process != ""){
                $queryBuilder = $queryBuilder->where('parent.PRO_UID', $process->uid);
            }
            if(isset($creator->value)){
                $queryBuilder = $queryBuilder->where('parent.APP_INIT_USER', $creator->value);
            }
        }

        $count = $queryBuilder->count();
        $queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));
        $applications = $queryBuilder->get();

        // $data = $applications->map(function($sub_application){
        //     return [
        //         'parent_case'       =>  $sub_application->parent->APP_NUMBER,
        //         'subprocess_case'   =>  ($sub_application->application) ? $sub_application->application->APP_NUMBER : "N/A",
        //         'case_title'        =>  $sub_application->parent->APP_TITLE,
        //         'process'           =>  $sub_application->parent->process->CON_VALUE,
        //         'creator'           =>  "{$sub_application->parent->creator->USR_FIRSTNAME} {$sub_application->parent->creator->USR_LASTNAME}"
        //     ];
        // });

        return [
            'count' =>  $count,
            'data'  =>  $applications
        ];
    }

    function getOldestDate(){
        $date = \App\Models\PM\Application::min('APP_CREATE_DATE');

        return $date;
    }

    function getOLDPMUsers(){
        return \App\Models\OLDPM\PMUser::where('USR_STATUS', 'ACTIVE')->get();
    }

    function searchOldPMCase(Request $request){
        $case_no = $request->case_no;

        $data = \App\Models\OLDPM\Application::where('APP_NUMBER', $case_no)->with('currentUser', 'delegations')->firstOrFail();

        return $data;
    }

    function reassignOLDPMCase(Request $request){
        $APP_UID = $request->APP_UID;
        $USR_UID = $request->USR_UID;

        $application = \App\Models\OLDPM\Application::where("APP_UID", $APP_UID)->firstOrFail();
        $application->APP_CUR_USER = $USR_UID;

        // $delegation = \App\Models\OLDPM\AppDelegation::where('APP_UID', $APP_UID)->where('DEL_THREAD_STATUS', 'OPEN')->firstOrFail();
        // $delegation->USR_UID = $USR_UID;

        $delegation = \DB::connection('old_pm')->statement("UPDATE app_delegation SET USR_UID = '{$USR_UID}' WHERE APP_UID = '{$APP_UID}' AND DEL_THREAD_STATUS = 'OPEN'");

        $application->save();

        return $application;
    }

    function downloadSignedApplication(Request $request){
        $id = $request->id;

        $document = \App\AdobeSignDocuments::where('AGREEMENT_STATUS', 'SIGNED')->where('id', $id)->first();

        if ($document) {
            $case = \App\Models\PM\Application::where('APP_NUMBER', $document->CASE_NO)->first();

            $template = FormTemplate::where('process', $case->PRO_UID)->first();
            \Log::debug("Downloading signed document. Agreement ID: {$document->AGREEMENT_ID}");

            $documentContent = AdobeClient::downloadSignedDocument($document->AGREEMENT_ID);
            $path = "adobe-sign/documents/{$document->CASE_NO}-signed.pdf";
            \Storage::put($path, $documentContent);

            \Log::debug("Signed Document saved in: " . storage_path("app/" . $path));

            $response = ProcessMaker::uploadGeneratedForm($case->APP_UID, $template->task, $template->input_document, $path);
            \Log::debug("ProcessMaker upload documents: " . json_encode($response));

            return \Storage::download($path);
        }

        die("The document has not been signed yet");
    }

    function getRevalidationCases(Request $request){
        $host_country_id = $request->host_country_id;
        $formType = $request->form_type;
        $system = $request->system;

        // dd($host_country_id ."=>".$formType."=>".$system);

        if ($system == "new") {
            if ($formType == "pro-1a") {
                $table = "DF_01";            
            }else if ($formType == "pro-1b") {
                $table = "DF_02";
            }else{
                $table = "DF_03";
            }

            return \DB::connection('pm_data')->table($table)->select('CASE_NO')->where("HOST_COUNTRY_ID", $host_country_id)->get();
        }else{
            $clientType = identify_hcsu_client($host_country_id);

            $index_no = "";

            switch ($clientType) {
                case 'staff':
                    $principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $host_country_id)->first();
                    $index_no = $principal->latest_contract->INDEX_NO;
                    break;

                case 'agency':
                    $index_no = (\App\Models\Agency::where('HOST_COUNTRY_ID', $host_country_id)->first())->ACRONYM;
                    break;

                case 'dependent':
                    $index_no = (\App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $host_country_id)->first())->principal->latest_contract->INDEX_NO;
                    break;
                
                default:
                    $index_no = 0;
                    break;
            }
            if ($formType == "pro-1a") {
                $table = "unon_sm_duty_free_liquor_tobacco_applications";            
            }else if ($formType == "pro-1b") {
                $table = "unon_sm_duty_free_goods_applications";
            }else{
                $table = "unon_sm_veh_duty_free_disposal_approval_application";
            }

            return \DB::connection('old_pm')->table($table)->select(\DB::raw('case_number AS case_no'))->where('index_no', $index_no)->get();
        }
    }
}
