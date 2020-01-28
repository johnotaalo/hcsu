<?php

namespace App\Helpers\HCSU\Data;

use \App\WorkPermitApplication;

class NOAData{
	public static function get($host_country_id){
		$data = new \StdClass;

		$clientType = identify_hcsu_client($host_country_id);
		if ($clientType == "staff") {
			$principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $host_country_id)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$principal->HOST_COUNTRY_ID})"))->first();

			$data->name = format_other_names($principal->OTHER_NAMES) . " " . strtoupper($principal->LAST_NAME);
			$data->mission = $contract->ACRONYM;
			$data->dob = $principal->DATE_OF_BIRTH;
			$data->designation = $contract->DESIGNATION;
			$data->grade = $contract->GRADE;
			$data->place_of_birth = $principal->PLACE_OF_BIRTH;
			$data->passport = ($principal->latest_passport) ? $principal->latest_passport : new \StdClass;
			$data->nationality = ($principal->nationality) ? $principal->nationality->official_name : "N/A";
			$data->marital_status = $principal->MARITAL_STATUS;
			$data->agency_details = \App\Models\Agency::where('ACRONYM', $contract->ACRONYM)->first();
			$data->phone = $principal->MOBILE_NO;
			$data->address = $principal->RESIDENCE;
			$data->arrival =($principal->current_arrival != null) ? $principal->current_arrival->ARRIVAL : "N/A";
			$data->dependents = $principal->dependents;

		}

		return $data;
	}
}