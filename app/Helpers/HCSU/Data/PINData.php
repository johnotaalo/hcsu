<?php

namespace App\Helpers\HCSU\Data;

use \App\PINApplication;

class PINData{
	public static function get($case_no){
		$data = new \StdClass;
		$clientObj = new \StdClass;

		$pinData = PINApplication::where('CASE_NO', $case_no)->first();
		$clientType = identify_hcsu_client($pinData->HOST_COUNTRY_ID); 
		$clientObj->type = $clientType;

		if($clientType == "agency"){
			$agency = \App\Models\Agency::where('HOST_COUNTRY_ID', $pinData->HOST_COUNTRY_ID)->first();
                  $name = $agency->ACRONYM;
                  $mission = $name;
                  $client_name = $name;
                  $arrival = "N/A";

                  $clientObj->name = $name;
                  $clientObj->organization = $mission;
                  $clientObj->arrival = $arrival;
		}else if ($clientType == "staff"){
                  $principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $pinData->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$pinData->HOST_COUNTRY_ID})"))->first();

                  $mission = $contract->ACRONYM;

                  $name = strtoupper($principal->LAST_NAME). ", " . ucwords(strtolower($principal->OTHER_NAMES)) ;
                  $client_name = $name;

                  $clientObj->name = $client_name;
                  $clientObj->organization = $mission;
                  $clientObj->contract_type = $contract->C_TYPE;
                  $clientObj->nationality = ($principal->nationality) ? $principal->nationality->name : "N/A";
                  $clientObj->passport = ($principal->latest_passport) ? $principal->latest_passport->PASSPORT_NO : "N/A";
		} else if ($clientType == "dependent"){
                  $dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $pinData->HOST_COUNTRY_ID)->first();

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
                  $clientObj->passport = ($dependent->latest_passport) ? $dependent->latest_passport->PASSPORT_NO : "N/A";;
            }

            $data->client = $clientObj;
            $data->case_no = $case_no;
            $data->ref = $pinData->NV_SERIAL_NO;
            $data->date = date('F d, Y');

		return $data;
	}
}