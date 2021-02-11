<?php

namespace App\Helpers\HCSU\Data;

use \App\WorkPermitApplication;
use \App\WorkPermitEndorsement;
use \App\WorkPermitRenewal;

class WorkPermitExemptionData{
	public static function get($case_no, $type = "new"){
		$endorsementCase = new \StdClass;
		if ($type == "new-case") {
			$caseData = WorkPermitApplication::where('CASE_NO', $case_no)->first();
		}elseif ($type == "endorsement") {
			$caseData = WorkPermitEndorsement::where('CASE_NO', $case_no)->first();
			$endorsementCase = WorkPermitApplication::where('ENDORSEMENT_CASE_NO', $case_no)->first();
		}elseif ($type == "renewal") {
			$caseData = WorkPermitRenewal::where('CASE_NO', $case_no)->first();
			// $renewalCase = WorkPermitApplication::where('ENDORSEMENT_CASE_NO', $case_no)->first();
		}
		
		$data = new \StdClass;
		$clientObj = new \StdClass;

		$clientType = identify_hcsu_client($caseData->HOST_COUNTRY_ID);
		$clientObj->type = $clientType;

		if($clientType == "staff"){
			$principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $caseData->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$caseData->HOST_COUNTRY_ID})"))->first();
			$mission = $contract->ACRONYM;
			$name = format_other_names($principal->OTHER_NAMES) . " " . strtoupper($principal->LAST_NAME);
			$client_name = $name;

			$includedDependents = explode(',', $caseData->DEPENDENTS);
			$dependents = [];
			$relationships = [];
			if (count($includedDependents)) {
				$dependents = \App\Models\PrincipalDependent::whereIn('HOST_COUNTRY_ID', $includedDependents)->get();
				$relationships = ($dependents->pluck('relationship.RELATIONSHIP'))->unique()->toArray();
			}

			if($type == "endorsement" && !$endorsementCase){
				$clientObj->RNO = $principal->R_NO;
				$data->endorsementType = "dependant_pass";
			}
			elseif($type == "endorsement" && $endorsementCase){
				$clientObj->RNO = $endorsementCase->RNUMBER;
				$data->endorsementType = "new_case";	
			}else{
				$clientObj->RNO = $principal->R_NO;
			}

			if($type == "renewal"){
				$clientObj->RNO = $principal->R_NO;
			}

			$clientObj->name = $client_name;
			$clientObj->designation = $contract->DESIGNATION;
			$clientObj->grade = $contract->GRADE;
			$clientObj->organization = $mission;
			$clientObj->contract_type = $contract->C_TYPE;
			$clientObj->nationality = ($principal->nationality) ? $principal->nationality->name : "N/A";
			$clientObj->passport = ($principal->latest_passport) ? $principal->latest_passport->PASSPORT_NO : "N/A";
			$clientObj->passport_validity = ($principal->latest_passport) ? $principal->latest_passport->EXPIRY_DATE : "N/A";
			$clientObj->dependents = $dependents;
			$clientObj->relationships = $relationships;
			$clientObj->gender = $principal->GENDER;
		}
		else if($clientType == "dependent"){
			$dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $caseData->HOST_COUNTRY_ID)->first();

			$relationship = $dependent->relationship->RELATIONSHIP;

			// $relationship = ($relationship == "Spouse") ? "s/o" : $relationship . " of";

			$name = ucwords(strtolower($dependent->OTHER_NAMES)). " " .strtoupper($dependent->LAST_NAME);
			$client_name = $name;
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$dependent->principal->HOST_COUNTRY_ID})"))->first();
			$mission = $contract->ACRONYM;

			$includedDependents = explode(',', $caseData->DEPENDENTS);
			$dependents = [];
			$relationships = [];
			if (count($includedDependents)) {
				$dependents = \App\Models\PrincipalDependent::whereIn('HOST_COUNTRY_ID', $includedDependents)->get();
				$relationships = ($dependents->pluck('relationship.RELATIONSHIP'))->unique()->toArray();
			}

			$clientObj->name = $client_name;
			$clientObj->designation = $contract->DESIGNATION;
			$clientObj->grade = $contract->GRADE;
			$clientObj->organization = $mission;
			$clientObj->contract_type = $contract->C_TYPE;
			$clientObj->principal = $dependent->principal;
			$clientObj->passport = $dependent->latest_passport;
			$clientObj->data = $dependent;
			$clientObj->allDependents = $dependents;
		}
		else if($clientType == "domestic-worker"){
			$domesticWorker = \App\Models\PrincipalDomesticWorker::where('HOST_COUNTRY_ID', $caseData->HOST_COUNTRY_ID)->first();

			$client_name = format_other_names($domesticWorker->OTHER_NAMES) . " " . $domesticWorker->LAST_NAME;
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$domesticWorker->PRINCIPAL_ID})"))->first();
			$mission = $contract->ACRONYM;

			if($type == "endorsement"){
				$clientObj->RNO = $endorsementCase->RNUMBER;
			}
			elseif($type == "renewal"){
				$clientObj->RNO = $domesticWorker->R_NO;
			}
			else{
				$clientObj->RNO = NULL;
				$data->endorsementType = "new_case";
			}

			$clientObj->name = $client_name;
			$clientObj->principal = $domesticWorker->principal;
			$clientObj->designation = $contract->DESIGNATION;
			$clientObj->grade = $contract->GRADE;
			$clientObj->organization = $mission;
			$clientObj->contract = $contract;
			$clientObj->contract_type = $contract->C_TYPE;
			$clientObj->nationality = ($domesticWorker->nationality) ? $domesticWorker->nationality->name : "N/A";
			$clientObj->passport = ($domesticWorker->latest_passport) ? $domesticWorker->latest_passport->PASSPORT_NO : "N/A";
			$clientObj->passport_validity = ($domesticWorker->latest_passport) ? $domesticWorker->latest_passport->EXPIRY_DATE : "N/A";
		}
		
		$data->client = $clientObj;
        $data->case_no = $case_no;
        $data->ref = $caseData->NV_SERIAL_NO;
        $data->date = date('F d, Y');
        $data->type = $type;
        $data->caseData = $caseData;

		return $data;
	}
}