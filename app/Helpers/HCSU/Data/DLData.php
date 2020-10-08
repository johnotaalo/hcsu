<?php

namespace App\Helpers\HCSU\Data;

use App\Models\NewDrivingLicense;

class DLData{
	public static function get($case_no){
		$caseData = NewDrivingLicense::where('CASE_NO', $case_no)->first();

		$data = new \StdClass;
		$clientObj = new \StdClass;
		$clientType = identify_hcsu_client($caseData->HOST_COUNTRY_ID);
		$clientObj->type = $clientType;

		if ($clientObj->type == "staff") {
			$principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $caseData->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$caseData->HOST_COUNTRY_ID})"))->first();

			$mission = $contract->ACRONYM;
			$name = format_other_names($principal->OTHER_NAMES) . " " . strtoupper($principal->LAST_NAME);
			$client_name = $name;
			$clientObj->name = $client_name;
			$clientObj->designation = $contract->DESIGNATION;
			$clientObj->fullname = "{$client_name}";
			$clientObj->contract_type = strtolower($contract->C_TYPE);
			$clientObj->organization = $mission;
			$clientObj->nationality = $principal->nationality->name;
		}else if($clientObj->type == "dependent"){
			$dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $caseData->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$dependent->PRINCIPAL_ID})"))->first();

			$mission = $contract->ACRONYM;
			
			$relationship = $dependent->relationship->RELATIONSHIP;
			$relationship = ($relationship == "Spouse") ? strtolower($relationship) : "dependent";

			$name = format_other_names($dependent->OTHER_NAMES) . " " . strtoupper($dependent->LAST_NAME);
			$client_name = $name;

			$clientObj->name = $client_name;

			$clientObj->fullname = "{$client_name}";
			$clientObj->principal = $dependent->principal;
			$clientObj->contract_type =strtolower( $contract->C_TYPE );
			$clientObj->organization = $mission;
			$clientObj->relationship = $relationship;
			$clientObj->nationality = $dependent->COUNTRY;
		}

		$data->client = $clientObj;
		$data->case_no = $case_no;
		$data->ref = $caseData->NV_SERIAL_NO;
		$data->date = date('F d, Y');
		$data->caseData = $caseData;

		return $data;
	}
}