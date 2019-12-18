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

use Storage;
use mikehaertl\pdftk\Pdf;

use App\Enums\UserType;

class AppController extends Controller
{
    function index(Request $request){
        // dd($request->query());
        if(\Auth::check()){
            if (\Auth::user()->user_type == UserType::getInstance(UserType::FocalPoint)) {
                return redirect()->route('focalpoints-home');
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

    function getAgencies(){
    	return Agency::all();
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

        if ($application == 'pin') {
            $clientType = identify_hcsu_client($host_country_id);
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
        

        $url = "http://".env('PM_SERVER')."/api/1.0/workflow/variable/{$case}/{$del_index}/variable/{$variable_name}";

        $data = [
            $variable_name => $host_country_id,
            $variable_name . '_label' => $host_country_id
        ];
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));

        $response = \Processmaker::executeREST($url, "PUT", $data, $authenticationData->access_token);

        // dd($response);

        return $response;
    }

    function getCaseVariable(Request $request){
        $case = $request->case_no;
        $del_index = 1;
        $variable_name = $request->variable;

        $url = "http://".env('PM_SERVER')."/api/1.0/workflow/variable/{$case}/{$del_index}/variable/{$variable_name}";

        $response = \Processmaker::executeREST($url, "GET", [], (new \Processmaker())->authData()->access_token);
        // dd($response);
        $caseVariable = get_object_vars($response);
        return $caseVariable;
    }

    function addTemplate(Request $request){
        $process = $request->input('process');
        $task = $request->input('task');
        $step = $request->input('step');
        $template = $request->file('file')->store("templates/{$process['prj_uid']}/{$task['act_uid']}");
        $path = storage_path('app/'. $template);

        $template = FormTemplate::create([
            "form_name"         => $request->input('name'),
            "input_document"    => $request->input('input_document'),
            "process"           => $process['prj_uid'],
            "task"              => $task['act_uid'],
            "step"              =>  $step['step_uid'],
            "path"              => $template
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

        return $data;
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

    function getProcessList(){
        $url = "http://".env('PM_SERVER')."/api/1.0/workflow/project";
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "GET", NULL, $authenticationData->access_token);

        return $response;
    }

    function getProcessTasks(Request $request){
        $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/project/" . $request->process;
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "GET", NULL, $authenticationData->access_token);

        return $response->diagrams[0]->activities;
    }

    function getTaskSteps(Request $request){
        $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/project/" . $request->process . "/activity/{$request->task}/steps";
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "GET", NULL, $authenticationData->access_token);
        
        return $response;
    }

    function generateDocument(Request $request){
        $case = $this->getCaseInformation($request->case_no);
        $applicationType = ($request->query('type')) ? $request->query('type') : "";
        // $variables = $this->getCaseVariables($request->case_no);

        // dd($case);
        $process = $case->pro_uid;
        $currentTask = $case->current_task[0]->tas_uid;

        if(!$applicationType){
            $document = FormTemplate::where([
                'process'   =>  $process,
                'task'      =>  $currentTask
            ])->first();
        }else{

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
            $data = $class->getData($case->app_number, $document);
            $filename = $class->getFileName();
            // $pdf->fillForm($data)
            //     ->needAppearances()
            //     ->saveAs("{$filename}.pdf");
            // $pdf->send("{$filename}", true);
            $pdf->fillForm($data)
                ->flatten()
                ->execute();

            $content = file_get_contents($pdf->getTmpFile());
            $localFile = "forms/{$process}/{$filename}-{$case->app_number}.pdf";
            \Storage::put($localFile, $content);
            // Upload to processmaker
            if ($document->input_document != null) {
                $this->uploadGeneratedForm($case->app_uid, $currentTask, $document, $localFile);
            }
            // return \Storage::download($localFile);
            return response()->file(storage_path("app/{$localFile}"));
        }
        else{
            abort(404);
        }
    }

    function generateNoteVerbal(Request $request){
        $downloadType = ($request->query('download')) ? (bool)$request->query('download') : false;
        $path = public_path();

        $case = $this->getCaseInformation($request->case_no);
        $creator = $case->app_init_usr_username;
        $creatorFrags = explode(" ", $creator);

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

        $pdf = new Pdf(public_path('templates/NV.pdf'), $config);
        $pdf->fillForm(['main_body' => $noteVerbal->getContent()])
                ->flatten()
                ->execute();

        $content = file_get_contents($pdf->getTmpFile());
        $localFile = "note-verbals/{$request->process}/Note Verbal - {$case->app_number}.pdf";
        \Storage::put($localFile, $content);

        $case = $this->getCaseInformation($request->case_no);
        $process = $case->pro_uid;
        $currentTask = $case->current_task[0]->tas_uid;
        $document = FormTemplate::where([
            'process'   =>  $process,
            'task'      =>  $currentTask
        ])->first();
        if($document){
            if ($document->input_document != null) {
                $this->uploadGeneratedForm($case->app_uid, $currentTask, $document, $localFile);
            }
        }

        if($downloadType){
            return \Storage::download($localFile);
        }
        return response()->file(storage_path("app/{$localFile}"));
    }

    function uploadGeneratedForm($case_no, $task_id, $document, $localFile){
        $inputDocuments = $this->getGeneratedDocuments($case_no);
        // // dd($inputDocuments);
        // if (count($inputDocuments)) {
        //     foreach ($inputDocuments as $inputDocument) {
        //         $this->deleteDocument($case_no, $inputDocument->app_doc_uid, $inputDocument->app_doc_index);
        //     }
        // }
        $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no . "/input-document";
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
        $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no . "/input-documents";
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "GET", NULL, $authenticationData->access_token);

        return $response;
    }

    function deleteDocument($case_no, $doc_id, $index){
        $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no . "/2/input-document/{$doc_id}";
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
        $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no;
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "GET", NULL, $authenticationData->access_token);
        // dd($response);

        return $response;
    }

    function getCaseVariables($case_no){
        $url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no . '/variables';
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = \Processmaker::executeREST($url, "GET", NULL, $authenticationData->access_token);

        return $response;
    }

    function downloadVATData(){
        $vat_data = \App\Model\VAT::all();

        return $vat_data;
    }
}
