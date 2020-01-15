<?php

namespace App\Helpers\HCSU\Data;

use \App\WorkPermitApplication;

class WorkPermitExemptionData{
	public static function get($case_no, $type = "new"){
		$caseData = WorkPermitApplication::where('CASE_NO', $case_no)->first();
		$data = new \StdClass;
		$clientObj = new \StdClass;

		$clientType = identify_hcsu_client($caseData->HOST_COUNTRY_ID);
		$clientObj->type = $clientType;

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
		
		$data->client = $clientObj;
        $data->case_no = $case_no;
        $data->ref = $caseData->NV_SERIAL_NO;
        $data->date = date('F d, Y');
        $data->type = $type;

		return $data;
	}
}