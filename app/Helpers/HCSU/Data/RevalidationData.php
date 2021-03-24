<?php

namespace App\Helpers\HCSU\Data;

use \App\Models\Revalidation;

class RevalidationData{
	public static function get($case_no){
		$caseData = Revalidation::where('CASE_NO', $case_no)->first();

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
			$clientObj->designation = "{$dependent->relationship->RELATIONSHIP} of {$dependent->principal->fullname}";
			$clientObj->fullname = "{$client_name} {$clientObj->designation}";
			$clientObj->principal = $dependent->principal;
			$clientObj->contract_type = $contract->C_TYPE;
			$clientObj->organization = $mission;
			$clientObj->relationship = $relationship;
		}

		$data->client = $clientObj;
		$data->case_no = $case_no;
		$data->caseData = $caseData;
		$data->ref = $caseData->NV_SERIAL_NO;
		$data->date = date('F d, Y');
		$data->applicationType = strtoupper(str_replace("-", "", $caseData->APPLICATION_TYPE));

		if ($caseData->APPLICATION_TYPE == "pro-1a") {
			$data->description = (\App\Helpers\HCSU\Data\Pro1AData::get($case_no))->description;
		} else if ($caseData->APPLICATION_TYPE == "pro-1b") {
			$data->description = (\App\Helpers\HCSU\Data\Pro1BData::get($case_no))->description;
		}else{
			$data->vehicle = (\App\Helpers\HCSU\Data\Pro1CData::get($case_no))->vehicle;
		}

		return $data;
	}
}