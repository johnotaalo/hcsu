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
try{
		if($firstIDChar == "1"){
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$blanketData->HOST_COUNTRY_ID})"))->first();
            $principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $blanketData->HOST_COUNTRY_ID)->first();

            $mission = $contract->ACRONYM;

            $name = strtoupper($principal->LAST_NAME). ", " . format_other_names($principal->OTHER_NAMES) ;
            $client_name = $name;

            $clientObj->name = $client_name;
            $clientObj->type = "staff";
            $clientObj->organization = $mission;
            $clientObj->pin = $principal->PIN_NO;
		}else if($firstIDChar == "3"){
			$agency = \App\Models\Agency::where('HOST_COUNTRY_ID', $blanketData->HOST_COUNTRY_ID)->first();
            $name = $agency->ACRONYM;
            $mission = $name;
            $client_name = $name;
            $arrival = "N/A";

            $clientObj->name = $name;
            $clientObj->organization = $mission;
            $clientObj->type = "agency";
            $clientObj->arrival = $arrival;
		}else if ($firstIDChar == "2"){
			$dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $blanketData->HOST_COUNTRY_ID)->first();

			$relationship = $dependent->relationship->RELATIONSHIP;

			$relationship = ($relationship == "Spouse") ? "s/o" : $relationship . " of";

			$c_name = strtoupper($dependent->LAST_NAME). ", " . format_other_names($dependent->OTHER_NAMES) . " {$relationship} {$dependent->principal->fullname}";
			$name = "{$c_name}; {$dependent->principal->latest_contract->DESIGNATION}";
			$mission = $dependent->principal->latest_contract->ACRONYM;
            $arrivalDate = ($dependent->principal->current_arrival) ? $dependent->principal->current_arrival->ARRIVAL : "N/A";
            $diplomaticCardNo = ($dependent->principal->latest_diplomatic_card) ? $dependent->principal->latest_diplomatic_card->DIP_ID_NO : "N/A";
            $arrival = "{$arrivalDate} (Dip. Id No: {$diplomaticCardNo})";
			// $arrival = ($dependent->principal->current_arrival) ? "{$dependent->principal->current_arrival->ARRIVAL} (Dip. Id No: {$dependent->principal->latest_diplomatic_card->DIP_ID_NO})" : "N/A";

			$clientObj->name = $c_name;
			$clientObj->designation = $dependent->principal->latest_contract->DESIGNATION;
			$clientObj->organization = $mission;
			$clientObj->index_no = $dependent->principal->latest_contract->INDEX_NO;
			$clientObj->type = "dependent";
			$clientObj->arrival = $arrival;
			$clientObj->pin = $principal->PIN;
		}
	}catch(\Exception $ex){
		die("There is a problem with this case no: {$case_no}");
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