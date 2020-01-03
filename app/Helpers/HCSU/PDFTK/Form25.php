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

		$tabData = [
			"fullname"			=>	$form_data->client->name,
			"pobox"				=>	$form_data->client->clientdata->ADDRESS,
			"dob"				=>	$form_data->client->clientdata->DATE_OF_BIRTH,
			"pob"				=>	$form_data->client->clientdata->PLACE_OF_BIRTH,
			"nationality"		=>	$form_data->client->nationality,
			"pptno"				=>	$form_data->client->passport->passport_no,
			"ppttype"			=>	$form_data->client->passport->passport_type,
			"doi"				=>	$form_data->client->passport->issue_date,
			"poi"				=>	$form_data->client->passport->place_of_issue,
			"spousename"		=>	ucwords(strtolower($form_data->client->clientdata->spouse->OTHER_NAMES)) . " " . strtoupper($form_data->client->clientdata->spouse->LAST_NAME),
			"rNo"				=>	$form_data->client->clientdata->R_NO,
			"agencyfullname"	=>	$form_data->client->contract->AGENCYNAME,
			"designation"		=>	$form_data->client->contract->DESIGNATION,
			"location"			=>	$form_data->client->contract->LOCATION,
			"descript"			=>	$form_data->client->contract->FUNC_TITLE,
			"cstart"			=>	$form_data->client->contract->START_DATE,
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