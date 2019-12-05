<?php

namespace App\Helpers\HCSU\Data;

use \App\DIPIDApplication;

class DiplomaticIDData{
	public static function get($case_no){
		$data = new \StdClass;
		$clientObj = new \StdClass;

		$dipidData = DIPIDApplication::where('CASE_NO', $case_no)->first();
		$clientType = identify_hcsu_client($dipidData->HOST_COUNTRY_ID);
		$clientObj->type = $clientType;

		if ($clientType == "staff"){
            $principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $dipidData->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$dipidData->HOST_COUNTRY_ID})"))->first();

			$mission = $contract->ACRONYM;

			$name = ucwords(strtolower($principal->OTHER_NAMES)) . " " . strtoupper($principal->LAST_NAME);
			$client_name = $name;

			$clientObj->name = $client_name;
			$clientObj->designation = $contract->DESIGNATION;
			$clientObj->grade = $contract->GRADE;
			$clientObj->organization = $mission;
			$clientObj->contract_type = $contract->C_TYPE;
			$clientObj->nationality = ($principal->nationality) ? $principal->nationality->name : "N/A";
			$clientObj->passport = ($principal->latest_passport) ? $principal->latest_passport->PASSPORT_NO : "N/A";
		} else if ($clientType == "dependent"){
			$dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $dipidData->HOST_COUNTRY_ID)->first();

			$relationship = $dependent->relationship->RELATIONSHIP;

			// $relationship = ($relationship == "Spouse") ? "s/o" : $relationship . " of";

			$c_name = ucwords(strtolower($dependent->OTHER_NAMES)). " " .strtoupper($dependent->LAST_NAME);
			// $name = "{$c_name}; {$dependent->principal->latest_contract->DESIGNATION}";
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$dependent->principal->HOST_COUNTRY_ID})"))->first();
			$mission = $contract->ACRONYM;
			// $arrival = ($dependent->principal->current_arrival) ? "{$dependent->principal->current_arrival->ARRIVAL} (Dip. Id No: {$dependent->principal->latest_diplomatic_card->DIP_ID_NO})" : "N/A";

			$clientObj->name = $c_name;
			$clientObj->organization = $mission;
			$clientObj->contract_type = $contract->C_TYPE;
			$clientObj->nationality = $dependent->COUNTRY;
			$clientObj->passport = $dependent->PASSPORT_NO;
			$clientObj->relationship = strtolower($relationship);
			$clientObj->principal = ucwords(strtolower($dependent->principal->OTHER_NAMES)) . " " . strtoupper($dependent->principal->LAST_NAME);
		}


		$data->client = $clientObj;
        $data->case_no = $case_no;
        $data->ref = $dipidData->NV_SERIAL_NO;
        $data->date = date('F d, Y');

		return $data;
	}
}