<?php

namespace App\Helpers\HCSU\Data;
/**
 * 
 */

use App\Models\StaffRegistration;

class StaffManagementData
{
	public static function get($case_no){
		$data = new \StdClass;
		$clientObj = new \StdClass;

		$staffRegistrationData = StaffRegistration::where('CASE_NO', $case_no)->first();

		$clientType = identify_hcsu_client($staffRegistrationData->HOST_COUNTRY_ID);

		$clientObj->type = $clientType;

		if ($clientType == "staff"){
			$principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $staffRegistrationData->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$staffRegistrationData->HOST_COUNTRY_ID})"))->first();

			$mission = $contract->ACRONYM;

			$name = format_other_names($principal->OTHER_NAMES) . " " . strtoupper($principal->LAST_NAME);
			$client_name = $name;

			$clientObj->name = $client_name;
			$clientObj->designation = $contract->DESIGNATION;
			$clientObj->grade = $contract->GRADE;
			$clientObj->organization = $mission;
			$clientObj->contract_type = $contract->C_TYPE;
			$clientObj->nationality = ($principal->nationality) ? $principal->nationality->name : "N/A";
			$clientObj->passport = ($principal->latest_passport) ? $principal->latest_passport->PASSPORT_NO : "N/A";
		}else if ($clientType == "dependent"){
			$dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $staffRegistrationData->HOST_COUNTRY_ID)->first();

			$relationship = $dependent->relationship->RELATIONSHIP;

			$c_name = ucwords(strtolower($dependent->OTHER_NAMES)). " " .strtoupper($dependent->LAST_NAME);
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$dependent->principal->HOST_COUNTRY_ID})"))->first();
			$mission = $contract->ACRONYM;

			$clientObj->name = $c_name;
			$clientObj->organization = $mission;
			$clientObj->contract_type = $contract->C_TYPE;
			$clientObj->nationality = $dependent->COUNTRY;
			$clientObj->passport = $dependent->PASSPORT_NO;
			$clientObj->relationship = strtolower($relationship);
			$clientObj->principal = ucwords(strtolower($dependent->principal->OTHER_NAMES)) . " " . strtoupper($dependent->principal->LAST_NAME);
		}

		$data->client = $clientObj;
		$data->staffRegistrationData = $staffRegistrationData;
		$data->case_no = $case_no;
        $data->ref = $staffRegistrationData->NV_SERIAL_NO;
        $data->date = date('F d, Y');

		return $data;
	}
}