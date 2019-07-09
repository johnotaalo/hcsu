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

use Storage;
use mikehaertl\pdftk\Pdf;

class AppController extends Controller
{
    function index(Request $request){
        $data = [];
        $data['iframe'] = false;
        $data['case_no'] = "";
        // echo "<pre>";print_r($request->url());die;
        if ($request->query('type') == "iframe") {
            $data['iframe'] = true;
        }

        if($request->query('case_no') != ""){
            $data['case_no'] = $request->query('case_no');
        }

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
        $del_index = 1;
        $variable_name = "host_country_id";
        $host_country_id = $request->host_country_id;


        $url = "http://10.104.104.87/api/1.0/workflow/variable/{$case}/{$del_index}/variable/{$variable_name}";

        $data = [
            $variable_name => $host_country_id,
            $variable_name . '_label' => $host_country_id
        ];
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));

        $response = \Processmaker::executeREST($url, "PUT", $data, $authenticationData->access_token);

        return $response;
    }

    function addTemplate(Request $request){
        $process = $request->input('process');
        $task = $request->input('task');
        $template = $request->file('file')->store("templates/{$process['prj_uid']}/{$task['act_uid']}");
        

        $pdf = new Pdf(storage_path($template));
        $data = $pdf->getDataFields();
        echo "<pre>";print_r($data);die;
        try{
            $res = $pdf->fillForm(['F[0].P1[0].#field[0]' => 'UNEP'])->needAppearances()->saveAs('pdffile.pdf');
            var_dump( $res );
        }catch(\Exception $ex){
            dd($ex);
        }
        
    }

    function getProcessList(){
        $url = "http://10.104.104.87/api/1.0/workflow/project";
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
}
