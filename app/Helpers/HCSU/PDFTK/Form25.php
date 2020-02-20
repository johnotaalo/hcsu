<?php

namespace App\Helpers\HCSU\PDFTK;

class Form25{
	private $filename;

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document){
		$form_data = \App\Helpers\HCSU\Data\Form25Data::get($case_no);
		$name = $filename = "";

		$date = date('F d, Y');

		$dependent_data = [];

		if($form_data->client->type == "staff"){
			foreach ($form_data->client->dependents as $dependent) {
				$no = 1;
				if ($dependent->RELATIONSHIP_ID != 2) {
					$dependent_data["owner{$no}"] = ucwords(strtolower($dependent->OTHER_NAMES)) . " " . strtoupper($dependent->LAST_NAME);
					$dependent_data["sex{$no}"] = $dependent->GENDER;
					$dependent_data["date_of_birth{$no}"] = date('Y-m-d', strtotime($dependent->DATE_OF_BIRTH));
					$dependent_data["nationality{$no}"] = $dependent->COUNTRY;
					$no++;
				}
			}
		}

		$spouseDetails = $form_data->client->clientdata->spouse;
		$spouse_name = "";
		if($form_data->client->clientdata->MARITAL_STATUS == "Single"){
			$spouse_name = ($form_data->client->clientdata->parent) ? format_other_names($form_data->client->clientdata->parent->OTHER_NAMES) . " " . strtoupper($form_data->client->clientdata->parent->LAST_NAME) . "({$form_data->client->clientdata->parent->relationship->RELATIONSHIP})" : "N/A";
		}else{
			if($spouseDetails){
				$spouse_name = format_other_names($spouseDetails->OTHER_NAMES) . " " . strtoupper($spouseDetails->LAST_NAME);
			}
		}

		$tabData = [
			"fullname"			=>	$form_data->client->name,
			"pobox"				=>	($form_data->client->type == "staff") ? $form_data->client->clientdata->ADDRESS : "{$form_data->client->clientdata->ADDRESS} c/o {$form_data->client->clientdata->principal->principal_name}",
			"dob"				=>	$form_data->client->clientdata->DATE_OF_BIRTH,
			"pob"				=>	$form_data->client->clientdata->PLACE_OF_BIRTH,
			"nationality"		=>	$form_data->client->nationality,
			"pptno"				=>	$form_data->client->passport->passport_no,
			"ppttype"			=>	$form_data->client->passport->passport_type,
			"doi"				=>	$form_data->client->passport->issue_date,
			"poi"				=>	$form_data->client->passport->place_of_issue,
			"spousename"		=>	($form_data->client->type == "staff") ? $spouse_name : "N/A",
			"rNo"				=>	$form_data->client->clientdata->R_NO,
			"agencyfullname"	=>	($form_data->client->type == "staff") ? $form_data->client->contract->AGENCYNAME : $form_data->client->clientdata->principal->principal_name,
			"designation"		=>	($form_data->client->type == "staff") ? $form_data->client->contract->DESIGNATION : "House Help",
			"location"			=>	($form_data->client->type == "staff") ? $form_data->client->contract->LOCATION : $form_data->client->clientdata->principal->RESIDENCE,
			"descript"			=>	($form_data->client->type == "staff") ? $form_data->client->contract->FUNC_TITLE : "Domestic Worker",
			"cstart"			=>	($form_data->client->type == "staff") ? $form_data->client->contract->START_DATE : $form_data->client->clientdata->CONTRACT_START_DATE,
			"cend"				=>	$form_data->client->contract->END_DATE,
			"today"				=>	$form_data->date
		];

		$tabData = $tabData + $dependent_data;

		// $template = new \App\Helpers\HCSU\PDFTK\Templates\VISAExtensionForm($tabData);
		// dd($tabData);
        $this->filename = "{$document->form_name} for {$form_data->client->name} - {$date}";
        return $tabData;
	}
}