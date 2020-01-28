<?php

namespace App\Helpers\HCSU\PDFTK;

class NOA{
	private $filename;

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function formData($host_country_id){
		$form_data = \App\Helpers\HCSU\Data\NOAData::get($host_country_id);
		$cleanedDependents = [];

		foreach ($form_data->dependents as $dependent) {
			$cleanedDependents[md5("{$dependent->LAST_NAME} {$dependent->OTHER_NAMES}")] = $dependent;
		}

		$spouse = (collect($cleanedDependents)->filter(function($dependent){
			if ($dependent->RELATIONSHIP_ID == 2) {
				return true;
			}
		}))->first();

		$otherDependents = (collect($cleanedDependents)->filter(function($dependent){
			$age = \Carbon\Carbon::parse($dependent->DATE_OF_BIRTH)->age;
			if ($dependent->RELATIONSHIP_ID != 2 && $age <= 23) {
				return true;
			}
		}))->all();

		$dependent_list = [];
		$dep_no = 1;
		foreach ($otherDependents as $dependent) {
			$gender = $dependent->GENDER;

			if (is_null($dependent->GENDER)) {
				if ($dependent->RELATIONSHIP_ID == 3) {
					$gender = "Male";
				}else{
					$gender = "Female";
				}
			}
			$dependent_list["owner{$dep_no}"] = format_other_names($dependent->OTHER_NAMES) . " " . strtoupper($dependent->LAST_NAME);
			$dependent_list["sex{$dep_no}"] = strtoupper($gender[0]);
			$dependent_list["age{$dep_no}"] = \Carbon\Carbon::parse($dependent->DATE_OF_BIRTH)->age;
			$dep_no++;
		}

		$tabData = [
			"fullname"			=>	$form_data->name,
			"agency"			=>	$form_data->mission,
			"dob"				=>	$form_data->dob,
			"designation"		=>	$form_data->designation,
			"grade"				=>	$form_data->grade,
			"pob"				=>	$form_data->place_of_birth,
			"pptno"				=>	($form_data->passport) ? $form_data->passport->PASSPORT_NO : "N/A",
			"ppttype"			=>	($form_data->passport) ? $form_data->passport->type->PPT_TYPE : "N/A",
			"nationality"		=>	$form_data->nationality,
			"marStatus"			=>	$form_data->marital_status,
			"location"			=>	($form_data->agency_details) ? $form_data->agency_details->LOCATION : "N/A",
			"pobox"				=>	($form_data->agency_details) ? "{$form_data->agency_details->POBOX}-{$form_data->agency_details->POSTCODE}" : "N/A",
			"office_phone_no"	=>	$form_data->phone,
			"address"			=>	$form_data->address,
			"mobile"			=>	$form_data->phone,
			"doa"				=>	$form_data->arrival,
			"v0"				=>	"N/A",
			"v1"				=>	"N/A",
			"m0"				=>	"N/A",
			"m1"				=>	"N/A",
			"spousename"		=>	format_other_names($spouse->OTHER_NAMES) . " " . strtoupper($spouse->LAST_NAME),
			"sp_emp"			=>	($spouse->EMPLOYMENT_DETAILS) ? $spouse->EMPLOYMENT_DETAILS : "N/A",
			"today"				=>	(\Carbon\Carbon::now())->format("F d, Y")

		];

		$tabData += $dependent_list;

		return $tabData;
	}
}