<?php

namespace App\Helpers\HCSU\Data;

use \App\Models\BlanketVAT;

class BlanketVATData{
	public static function get($case_no){
		$data = new \StdClass;

		$vatObj = new \StdClass;
		$clientObj = new \StdClass;

		$blanketData = BlanketVAT::where('CASE_NO', $case_no)->first();
		$firstIDChar = substr($blanketData->HOST_COUNTRY_ID, 0, 1);
		$year = date('Y', strtotime($blanketData->DURATION_FROM));

		if($firstIDChar == "1"){
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$blanketData->HOST_COUNTRY_ID})"))->first();
            $principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $blanketData->HOST_COUNTRY_ID)->first();

            $mission = $contract->ACRONYM;

            $name = strtoupper($principal->LAST_NAME). ", " . ucwords(strtolower($principal->OTHER_NAMES)) ;
            $client_name = $name;

            $clientObj->name = $client_name;
            $clientObj->type = "staff";
            $clientObj->organization = $mission;
		}else if($firstIDChar == "3"){
			$agency = \App\Models\Agency::where('HOST_COUNTRY_ID', $vat_data->HOST_COUNTRY_ID)->first();
            $name = $agency->ACRONYM;
            $mission = $name;
            $client_name = $name;
            $arrival = "N/A";

            $clientObj->name = $name;
            $clientObj->organization = $mission;
            $clientObj->type = "agency";
            $clientObj->arrival = $arrival;
		}

		$durationFrom = new \Carbon\Carbon($blanketData->DURATION_FROM);
		$durationTo = new \Carbon\Carbon($blanketData->DURATION_TO);

		$period = $durationFrom->diffInMonths($durationTo);

		$data->client = $clientObj;
		$data->case_no = $case_no;
		$data->year = $year;
		$data->ref = "BLANKET_VAT/{$blanketData->supplier->ABBREV}/{$year}/{$case_no}";
		$data->vatObj = $blanketData;
		$data->date = date('F d, Y', strtotime($blanketData->CREATED_AT));
		$data->period = $period;

		return $data;
	}
}