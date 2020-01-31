<?php

namespace App\Helpers\HCSU\Data;

use \App\WorkPermitApplication;

class Form25Data{
	public static function get($case_no){
		$data = new \StdClass;
		$clientObj = new \StdClass;

		$case_data = WorkPermitApplication::where('CASE_NO', $case_no)->first();
		$clientType = identify_hcsu_client($case_data->HOST_COUNTRY_ID); 
		$clientObj->type = $clientType;
		$clientObj->passport = new \StdClass;

		if ($clientType == "staff"){
            $principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $case_data->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$case_data->HOST_COUNTRY_ID})"))->first();

	        $mission = $contract->ACRONYM;

	        $name = format_other_names($principal->OTHER_NAMES) . " " . strtoupper($principal->LAST_NAME);
	        $client_name = $name;

	        $clientObj->name = $client_name;
	        $clientObj->clientdata = $principal;
	        $clientObj->organization = $mission;
	        $clientObj->contract_type = $contract->C_TYPE;
	        $clientObj->contract = $contract;
	        $clientObj->nationality = ($principal->nationality) ? $principal->nationality->official_name : "N/A";
	        $clientObj->passport->passport_no = ($principal->latest_passport) ? $principal->latest_passport->PASSPORT_NO : "N/A";
	        $clientObj->passport->issue_date = ($principal->latest_passport) ? $principal->latest_passport->ISSUE_DATE : "N/A";
	        $clientObj->passport->place_of_issue = ($principal->latest_passport) ? $principal->latest_passport->PLACE_OF_ISSUE : "N/A";
	        $clientObj->passport->passport_type = ($principal->latest_passport) ? $principal->latest_passport->type->PPT_TYPE : "N/A";
	        $clientObj->dependents = $principal->dependents;
		}else if($clientType == "domestic-worker"){
			$domesticWorker = \App\Models\PrincipalDomesticWorker::where('HOST_COUNTRY_ID', $case_data->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$domesticWorker->principal->HOST_COUNTRY_ID})"))->first();

	        $mission = $contract->ACRONYM;

	        $name = format_other_names($domesticWorker->OTHER_NAMES) . " " . strtoupper($domesticWorker->LAST_NAME);
	        $client_name = $name;

	        $clientObj->name = $client_name;
	        $clientObj->clientdata = $domesticWorker;
	        $clientObj->organization = $mission;
	        $clientObj->contract_type = $contract->C_TYPE;
	        $clientObj->contract = $contract;
	        $clientObj->nationality = ($domesticWorker->nationality) ? $domesticWorker->nationality->official_name : "N/A";
	        $clientObj->passport->passport_no = ($domesticWorker->latest_passport) ? $domesticWorker->latest_passport->PASSPORT_NO : "N/A";
	        $clientObj->passport->issue_date = ($domesticWorker->latest_passport) ? $domesticWorker->latest_passport->ISSUE_DATE : "N/A";
	        $clientObj->passport->place_of_issue = ($domesticWorker->latest_passport) ? $domesticWorker->latest_passport->PLACE_OF_ISSUE : "N/A";
	        $clientObj->passport->passport_type = ($domesticWorker->latest_passport) ? $domesticWorker->latest_passport->type->PPT_TYPE : "N/A";
		}

        $data->client = $clientObj;
        $data->case_no = $case_no;
        $data->ref = $case_data->SERIAL_NO;
        $data->date = date('F d, Y');
        $data->case_data = $case_data;

		return $data;
	}
}