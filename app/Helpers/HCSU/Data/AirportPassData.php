<?php

namespace App\Helpers\HCSU\Data;

use App\Models\AirportPass;

class AirportPassData{
	public static function get($case_no, $type = "new"){
		$data = new \StdClass;
		$clientObj = new \StdClass;

		$airportPassData = ($type == "new") ? AirportPass::where('CASE_NO', $case_no)->first() : NULL;

		$principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $airportPassData->HOST_COUNTRY_ID)->first();
		$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$airportPassData->HOST_COUNTRY_ID})"))->first();

		$mission = $contract->ACRONYM;

		$name = format_other_names($principal->OTHER_NAMES) . " " . strtoupper($principal->LAST_NAME);
		$client_name = $name;

		$clientObj->name = $client_name;
		$clientObj->designation = $contract->DESIGNATION;
		$clientObj->organization = $mission;
		$clientObj->contract_type = $contract->C_TYPE;
		$clientObj->passport = ($principal->latest_passport) ? $principal->latest_passport->PASSPORT_NO : "N/A";
		$clientObj->doa = ($principal->current_arrival) ? $principal->current_arrival->ARRIVAL : "N/A";
		$clientObj->principal = $principal;
		$clientObj->applicationYear = $airportPassData->YEAR;
		$clientObj->contact = $principal->MOBILE_NO;
		$clientObj->nationality = $principal->nationality->name;

		$data->client = $clientObj;
		$data->case_no = $case_no;
        $data->ref = $airportPassData->NV_SERIAL_NO;
        $data->date = date('F d, Y');
        $data->type = $type;
        $data->actual = $airportPassData;

		return $data;
	}
}