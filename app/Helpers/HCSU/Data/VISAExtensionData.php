<?php

namespace App\Helpers\HCSU\Data;

use \App\VisaExtensionApplication;

class VISAExtensionData{
	public static function get($case_no){
		$data = new \StdClass;
		$clientObj = new \StdClass;

		$case_data = VisaExtensionApplication::where('CASE_NO', $case_no)->first();
		$clientType = identify_hcsu_client($case_data->HOST_COUNTRY_ID); 
		$clientObj->type = $clientType;
		$clientObj->passport = new \StdClass;

		if ($clientType == "staff"){
            $principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $case_data->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$case_data->HOST_COUNTRY_ID})"))->first();

	        $mission = $contract->ACRONYM;

	        $name = strtoupper($principal->LAST_NAME). ", " . ucwords(strtolower($principal->OTHER_NAMES)) ;
	        $client_name = $name;

	        $clientObj->name = $client_name;
	        $clientObj->clientdata = $principal;
	        $clientObj->organization = $mission;
	        $clientObj->contract_type = $contract->C_TYPE;
	        $clientObj->nationality = ($principal->nationality) ? $principal->nationality->official_name : "N/A";
	        $clientObj->passport->passport_no = ($principal->latest_passport) ? $principal->latest_passport->PASSPORT_NO : "N/A";
	        $clientObj->passport->issue_date = ($principal->latest_passport) ? $principal->latest_passport->ISSUE_DATE : "N/A";
	        $clientObj->passport->place_of_issue = ($principal->latest_passport) ? $principal->latest_passport->PLACE_OF_ISSUE : "N/A";
		} else if ($clientType == "dependent"){
          $dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $case_data->HOST_COUNTRY_ID)->first();

          $relationship = $dependent->relationship->RELATIONSHIP;

          $relationship = ($relationship == "Spouse") ? "s/o" : $relationship . " of";

          $c_name = strtoupper($dependent->LAST_NAME). ", " . ucwords(strtolower($dependent->OTHER_NAMES)) . " {$relationship} {$dependent->principal->fullname}";
          $name = "{$c_name}; {$dependent->principal->latest_contract->DESIGNATION}";
          $mission = $dependent->principal->latest_contract->ACRONYM;
          // $arrival = ($dependent->principal->current_arrival) ? "{$dependent->principal->current_arrival->ARRIVAL} (Dip. Id No: {$dependent->principal->latest_diplomatic_card->DIP_ID_NO})" : "N/A";
          $contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$dependent->principal->HOST_COUNTRY_ID})"))->first();

          $clientObj->name = $c_name;
          $clientObj->organization = $mission;
          $clientObj->contract_type = $contract->C_TYPE;
          $clientObj->nationality = $dependent->COUNTRY;
          $clientObj->passport_no = $dependent->PASSPORT_NO;
        }

        $data->client = $clientObj;
        $data->case_no = $case_no;
        $data->ref = $case_data->SERIAL_NO;
        $data->date = date('F d, Y');
        $data->case_data = $case_data;

		return $data;
	}
}