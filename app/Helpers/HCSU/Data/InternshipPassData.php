<?php

namespace App\Helpers\HCSU\Data;

use \App\InternshipPassApplications;

class InternshipPassData{
	public static function get($case_no){
		$data = new \StdClass;
		$clientObj = new \StdClass;

		$case_data = InternshipPassApplications::where('CASE_NO', $case_no)->first();

		$clientType = identify_hcsu_client($case_data->HOST_COUNTRY_ID);
		$clientObj->type = $clientType;
		$clientObj->passport = new \StdClass;

		if ($clientType == "staff"){
			$principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $case_data->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$case_data->HOST_COUNTRY_ID})"))->first();
			$mission = $contract->ACRONYM;
			$gender_title = "";
			if ($principal->GENDER == "Male") {
				$gender_title = "Mr";
			}else{
				$gender_title = "Ms";
			}

			$name = "{$gender_title}. " . format_other_names($principal->OTHER_NAMES) . " " . strtoupper($principal->LAST_NAME);
			$client_name = $name;

			$clientObj->name = $client_name;
			$clientObj->gender = $principal->GENDER;
			$clientObj->nationality = ($principal->nationality) ? $principal->nationality->official_name : "N/A";
			$clientObj->passport->passport_no = ($principal->latest_passport) ? $principal->latest_passport->PASSPORT_NO : "N/A";
			$clientObj->contract = $contract;
		}


		$data->client = $clientObj;
		$data->case_no = $case_no;
        $data->ref = $case_data->NV_SERIAL;
        $data->date = date('F d, Y');
        $data->case_data = $case_data;
		return $data;
	}
}