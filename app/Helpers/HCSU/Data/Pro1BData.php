<?php

namespace App\Helpers\HCSU\Data;

use \App\Models\Pro1B;

class Pro1BData{
	public static function get($case_no){
		$caseData = Pro1B::where('CASE_NO', $case_no)->first();
		// dd($caseData);
		$data = new \StdClass;
		$clientObj = new \StdClass;
		$clientType = identify_hcsu_client($caseData->HOST_COUNTRY_ID);
		$clientObj->type = $clientType;
		$description = "";

		if ($clientObj->type == "staff") {
			$principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $caseData->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$caseData->HOST_COUNTRY_ID})"))->first();

			$mission = $contract->ACRONYM;
			$name = format_other_names($principal->OTHER_NAMES) . " " . strtoupper($principal->LAST_NAME);
			$client_name = $name;

			$arrivalDate = ($principal->current_arrival) ? $principal->current_arrival->ARRIVAL : "N/A";

			$clientObj->name = $client_name;
			$clientObj->designation = $contract->DESIGNATION;
			$clientObj->fullname = "{$client_name}";
			$clientObj->contract_type = $contract->C_TYPE;
			$clientObj->organization = $mission;
			$clientObj->doa = $arrivalDate;
		}else if($clientObj->type == "agency"){
			$agency = \App\Models\Agency::where('HOST_COUNTRY_ID', $caseData->HOST_COUNTRY_ID)->first();
			$clientObj->name = $agency->ACRONYM;
			$clientObj->fullname = $agency->AGENCYNAME;
			$clientObj->organization = $agency->ACRONYM;
		}else if($clientObj->type == "dependent"){
			$dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $caseData->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$dependent->PRINCIPAL_ID})"))->first();

			$mission = $contract->ACRONYM;
			
			$relationship = $dependent->relationship->RELATIONSHIP;
			$relationship = ($relationship == "Spouse") ? strtolower($relationship) : "dependent";

			$name = format_other_names($dependent->OTHER_NAMES) . " " . strtoupper($dependent->LAST_NAME);
			$client_name = $name;

			$clientObj->name = $client_name;
			$clientObj->designation = $contract->DESIGNATION;
			$clientObj->fullname = "{$client_name} {$contract->DESIGNATION}";
			$clientObj->principal = $dependent->principal;
			$clientObj->contract_type = $contract->C_TYPE;
			$clientObj->organization = $mission;
			$clientObj->relationship = $relationship;

			$arrivalDate = ($dependent->principal->current_arrival) ? $dependent->principal->current_arrival->ARRIVAL : "N/A";
			$clientObj->doa = $arrivalDate;
		}

		$data->client = $clientObj;
		$data->case_no = $case_no;
		$data->ref = $caseData->NV_SERIAL_NO;
		$data->date = date('F d, Y');
		$data->caseData = $caseData;
		$data->description = $caseData->MERCHANDISE_DESCRIPTION;

		return $data;
	}
}