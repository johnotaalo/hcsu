<?php

namespace App\Helpers\HCSU\Data;

use \App\Models\Pro1A;

class Pro1AData{
	public static function get($case_no){
		$caseData = Pro1A::where('CASE_NO', $case_no)->first();
		$data = new \StdClass;
		$clientObj = new \StdClass;
		$clientType = identify_hcsu_client($caseData->HOST_COUNTRY_ID);
		$clientObj->type = $clientType;
		$description = "";

		$descriptionArray = $descriptionArrayCleaned = [];

		$descriptionArray = [
			'Spirits'	=>	$caseData->SPIRITS,
			'Wine'		=>	$caseData->WINES,
			'Beer'		=>	$caseData->BEERS,
			'Tobacco'	=>	$caseData->TOBACCO
		];

		foreach ($descriptionArray as $key => $value) {
			if ($value != 0) {
				$descriptionArrayCleaned[] = "{$key}: {$value} Pieces";
			}
		}

		$description = implode(", ", $descriptionArrayCleaned);

		if ($clientObj->type == "staff") {
			$principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $caseData->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$caseData->HOST_COUNTRY_ID})"))->first();

			$mission = $contract->ACRONYM;
			$name = format_other_names($principal->OTHER_NAMES) . " " . strtoupper($principal->LAST_NAME);
			$client_name = $name;

			$clientObj->name = $client_name;
			$clientObj->designation = $contract->DESIGNATION;
			$clientObj->fullname = "{$client_name}";
			$clientObj->contract_type = $contract->C_TYPE;
			$clientObj->organization = $mission;
		}else if($clientObj->type == "agency"){
			$agency = \App\Models\Agency::where('HOST_COUNTRY_ID', $caseData->HOST_COUNTRY_ID)->first();
			$clientObj->name = $agency->ACRONYM;
			$clientObj->fullname = $agency->AGENCYNAME;
			$clientObj->organization = $agency->ACRONYM;
		}else if($clientObj->type == "dependent"){
			$dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $caseData->HOST_COUNTRY_ID)->first();
			$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$dependent->PRINCIPAL_ID})"))->first();

			$mission = $contract->ACRONYM;
			
			$relationship = $dependent->relationship->RELATIONSHIP;
			$relationship = ($relationship == "Spouse") ? strtolower($relationship) : "dependent";

			$name = format_other_names($dependent->OTHER_NAMES) . " " . strtoupper($dependent->LAST_NAME);
			$client_name = $name;

			$clientObj->name = $client_name;
			$clientObj->designation = "{$dependent->relationship->RELATIONSHIP} of {$dependent->principal->fullname}";
			$clientObj->fullname = "{$client_name} {$clientObj->designation}";
			$clientObj->principal = $dependent->principal;
			$clientObj->contract_type = $contract->C_TYPE;
			$clientObj->organization = $mission;
			$clientObj->relationship = $relationship;
		}

		$data->client = $clientObj;
		$data->case_no = $case_no;
		$data->ref = $caseData->NV_SERIAL_NO;
		$data->date = date('F d, Y');
		$data->caseData = $caseData;
		$data->description = $description;

		return $data;
	}
}