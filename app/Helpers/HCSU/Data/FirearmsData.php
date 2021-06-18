<?php

namespace App\Helpers\HCSU\Data;

use App\Models\FireArmsApplication;

class FirearmsData{
	public static function get($case_no){
		$data = new \StdClass;
		$clientObj = new \StdClass;

		$firearmsData = FireArmsApplication::where('CASE_NO', $case_no)->firstOrFail();

		$clientType = identify_hcsu_client($firearmsData->HOST_COUNTRY_ID);
		$clientObj->type = $clientType;

		if ($clientType == "staff") {
			$principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $firearmsData->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$firearmsData->HOST_COUNTRY_ID})"))->first();

			$mission = $contract->ACRONYM;
			$name = format_other_names($principal->OTHER_NAMES) . " " . strtoupper($principal->LAST_NAME);
			$client_name = $name;

			$arrivalDate = ($principal->current_arrival) ? $principal->current_arrival->ARRIVAL : "N/A";

			$clientObj->name = $client_name;
			$clientObj->designation = $contract->DESIGNATION;
			$clientObj->fullname = "{$client_name}";
			$clientObj->contract_type = $contract->C_TYPE;
			$clientObj->organization = $mission;
		}elseif($clientType == "agency"){
			$agency = \App\Models\Agency::where('HOST_COUNTRY_ID', $firearmsData->HOST_COUNTRY_ID)->first();
			$clientObj->name = $agency->ACRONYM;
			$clientObj->fullname = $agency->AGENCYNAME;
			$clientObj->organization = $agency->ACRONYM;
		}

		$data->client = $clientObj;
		$data->firearmsData = $firearmsData;
		$data->case_no = $case_no;
		$data->ref = $firearmsData->NV_SERIAL_NO;
		$data->date = date('F d, Y');

		return $data;
	}
}