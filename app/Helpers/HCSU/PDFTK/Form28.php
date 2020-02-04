<?php

namespace App\Helpers\HCSU\PDFTK;

class Form28{
	private $filename;

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document, $extraParams){
		$dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $extraParams['host_country_id'])->first();
		$casedata = \App\WorkPermitApplication::where('CASE_NO', $case_no)->first();
		$dependentsArray = explode(",", $casedata->DEPENDENTS);
		$otherDependents = collect($dependentsArray)->filter(function($dep) use ($extraParams){
			return $dep != $extraParams['host_country_id'];
		})->toArray();

		$date = date('F d, Y');

		$this->filename = "{$document->form_name} for {$dependent->LAST_NAME} {$dependent->OTHER_NAMES}- {$date}";
		$data =  [
			'fullname' 			=>	ucwords(strtolower($dependent->principal->OTHER_NAMES)) . " " . strtoupper($dependent->principal->LAST_NAME),
			'mobile'			=>	$dependent->principal->MOBILE_NO,
			'pobox_email'		=>	$dependent->principal->ADDRESS . " {$dependent->principal->EMAIL}",
			'nationality'		=>	$dependent->principal->nationality->official_name,
			'rNo'				=>	($dependent->RNO) ? $dependent->RNO : "N/A",
			'drelationship'		=>	$dependent->relationship->RELATIONSHIP,
			'depfullname'		=>	ucwords(strtolower($dependent->OTHER_NAMES)) . " " . strtoupper($dependent->LAST_NAME),
			'address'			=>	$dependent->principal->RESIDENCE,
			'dsex'				=>	$dependent->GENDER,
			'dmaritalstatus'	=>	($dependent->relationship->RELATIONSHIP == "Spouse") ? "Married" : "Sigle",
			'ddob'				=>	date('Y-m-d', strtotime($dependent->DATE_OF_BIRTH)),
			'dpob'				=>	$dependent->PLACE_OF_BIRTH,
			'dpptno'			=>	($dependent->latest_passport) ? $dependent->latest_passport->PASSPORT_NO : "N/A",
			'ddoi'				=>	($dependent->latest_passport) ? $dependent->latest_passport->ISSUE_DATE : "N/A",
			'dpoi'				=>	($dependent->latest_passport) ? $dependent->latest_passport->PLACE_OF_ISSUE : "N/A",
			'dnationality'		=>	$dependent->COUNTRY
		];

		$otherDependentsData = [];

		if ($otherDependents) {
			$key = 1;
			foreach ($otherDependents as $dependent_id) {
				$innerKey = "dep{$key}";
				$depData = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $dependent_id)->first();
				$otherDependentsData["{$innerKey}_fullname"]		=	$depData->fullname;
				$otherDependentsData["{$innerKey}_relationship"]	=	$depData->relationship->RELATIONSHIP;
				$otherDependentsData["{$innerKey}_age"]				=	\Carbon\Carbon::parse($depData->DATE_OF_BIRTH)->age;;
				$otherDependentsData["{$innerKey}_residence"]		=	$dependent->principal->RESIDENCE;
			}
		}

		$data = $data + $otherDependentsData;

		return $data;
	}
}