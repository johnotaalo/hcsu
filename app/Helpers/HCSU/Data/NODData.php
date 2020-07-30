<?php

namespace App\Helpers\HCSU\Data;

use App\Models\NOD;

class NODData{
	public static function get($case_no){
		$caseData = NOD::where('CASE_NO', $case_no)->first();

		$data = new \StdClass;
		$clientObj = new \StdClass;

		$principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $caseData->HOST_COUNTRY_ID)->first();
		$contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$caseData->HOST_COUNTRY_ID})"))->first();

		$mission = $contract->ACRONYM;
		$name = format_other_names($principal->OTHER_NAMES) . " " . strtoupper($principal->LAST_NAME);
		$client_name = $name;

		$clientObj->name = $client_name;
		$clientObj->designation = $contract->DESIGNATION;
		$clientObj->grade = $contract->GRADE;
		$clientObj->fullname = "{$client_name}";
		$clientObj->contract_type = $contract->C_TYPE;
		$clientObj->organization = $mission;
		$clientObj->nationality = $principal->nationality->official_name;

		$dependents = $caseData->DEPENDENTS;

		if ($dependents) {
			$dependentsFrags = explode(",", $dependents);
			$dependentData = collect($dependentsFrags)->map(function($dep){
				$data = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $dep)->first();
				return [
					'name'			=>	format_other_names($data->OTHER_NAMES) . " " . strtoupper($data->LAST_NAME),
					'nationality'	=>	$data->COUNTRY,
					'title'			=>	$data->relationship->RELATIONSHIP,
					'type'			=>	($data->relationship->RELATIONSHIP == "Spouse") ? "Spouse" : "Dependent"
				];
			});

			$relationships = $dependentData->pluck('type')->unique();
			$genderLine = ($principal->GENDER == "Male") ? "his" : "her";
			$dependentLine = "";

			if ($relationships->contains('Spouse') && $relationships->contains('Dependent')) {
				$dependentLine = "spouse & dependent(s)";
			} else if ($relationships->contains('Spouse') && !$relationships->contains('Dependent')) {
				$dependentLine = "spouse";
			} else if (!$relationships->contains('Spouse') && $relationships->contains('Dependent')) {
				$dependentLine = "dependent(s)";
			}

			$includeDependents = "and {$genderLine} {$dependentLine}";

			$clientObj->dependents = $dependentData;
			$clientObj->include = $includeDependents;
			$clientObj->dependentLine = $dependentLine;
		}

		$data->client = $clientObj;
		$data->case_no = $case_no;
		$data->ref = $caseData->NV_SERIAL_NO;
		$data->date = date('F d, Y');
		$data->caseData = $caseData;
		$data->dependents = $dependents;

		return $data;
	}
}