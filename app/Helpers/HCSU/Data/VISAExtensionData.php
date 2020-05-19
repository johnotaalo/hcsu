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

	        $name = strtoupper($principal->LAST_NAME). ", " . format_other_names($principal->OTHER_NAMES) ;
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

          $c_name = strtoupper($dependent->LAST_NAME). ", " . format_other_names($dependent->OTHER_NAMES) . " {$relationship} {$dependent->principal->fullname}";
          $name = "{$c_name}; {$dependent->principal->latest_contract->DESIGNATION}";
          $mission = $dependent->principal->latest_contract->ACRONYM;
          // $arrival = ($dependent->principal->current_arrival) ? "{$dependent->principal->current_arrival->ARRIVAL} (Dip. Id No: {$dependent->principal->latest_diplomatic_card->DIP_ID_NO})" : "N/A";
          $contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$dependent->principal->HOST_COUNTRY_ID})"))->first();

          $clientObj->name = $c_name;
          $clientObj->clientdata = $dependent;
          $clientObj->organization = $mission;
          $clientObj->contract_type = $contract->C_TYPE;
          $clientObj->nationality = $dependent->COUNTRY;
          $clientObj->passport->passport_no = ($dependent->latest_passport) ? $dependent->latest_passport->PASSPORT_NO : "N/A";
          $clientObj->passport->issue_date = ($dependent->latest_passport) ? $dependent->latest_passport->ISSUE_DATE : "N/A";
	      $clientObj->passport->place_of_issue = ($dependent->latest_passport) ? $dependent->latest_passport->PLACE_OF_ISSUE : "N/A";
        }

        else if ($clientType == "domestic-worker"){
        	$domesticWorker = \App\Models\PrincipalDomesticWorker::where('HOST_COUNTRY_ID', $case_data->HOST_COUNTRY_ID)->first();

        	$c_name = strtoupper($domesticWorker->LAST_NAME). ", " . format_other_names($domesticWorker->OTHER_NAMES) . " domestic worker of {$domesticWorker->principal->fullname}";
        	$mission = $domesticWorker->principal->latest_contract->ACRONYM;
        	$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$domesticWorker->principal->HOST_COUNTRY_ID})"))->first();

			$clientObj->name = $c_name;
			$clientObj->clientdata = $domesticWorker;
			$clientObj->organization = $mission;
			$clientObj->contract_type = $contract->C_TYPE;
			$clientObj->nationality = $domesticWorker->nationality->name;
			$clientObj->passport->passport_no = ($domesticWorker->latest_passport) ? $domesticWorker->latest_passport->PASSPORT_NO : "N/A";
			$clientObj->passport->issue_date = ($domesticWorker->latest_passport) ? $domesticWorker->latest_passport->ISSUE_DATE : "N/A";
			$clientObj->passport->place_of_issue = ($domesticWorker->latest_passport) ? $domesticWorker->latest_passport->PLACE_OF_ISSUE : "N/A";
        }

        else if($clientType == "other-clients"){
        	$otherClients = \App\Models\OtherClient::where('HOST_COUNTRY_ID', $case_data->HOST_COUNTRY_ID)->with('passportCountry')->first();
        	$country_of_issue = \App\Models\Country::where('id', $otherClients->COUNTRY_OF_ISSUE)->first();

        	$c_name = strtoupper($otherClients->LAST_NAME). ", " . format_other_names($otherClients->OTHER_NAMES);
        	$mission = $otherClients->agency->ACRONYM;

        	$clientObj->name = $c_name;
			$clientObj->clientdata = $otherClients;
			$clientObj->organization = $mission;
			$clientObj->contract_type = $otherClients->TYPE;
			$clientObj->nationality = $otherClients->nationality->name;
			$clientObj->passport->passport_no = $otherClients->PASSPORT_NO;
			$clientObj->passport->issue_date = $otherClients->ISSUE_DATE;
			$clientObj->passport->place_of_issue = $country_of_issue->name;
        }

        $data->client = $clientObj;
        $data->case_no = $case_no;
        $data->ref = $case_data->SERIAL_NO;
        $data->date = date('F d, Y');
        $data->case_data = $case_data;

		return $data;
	}
}