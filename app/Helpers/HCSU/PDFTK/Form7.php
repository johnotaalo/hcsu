<?php

namespace App\Helpers\HCSU\PDFTK;

class Form7{
	private $filename = "Form VII";

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document, $extraParams = null){
		$form_data = \App\Helpers\HCSU\Data\DLData::get($case_no);
		$date = date("F d, Y");
		$this->filename = "Form VII for {$form_data->client->name} {$date}";

		$age = \Carbon\Carbon::parse($form_data->client->dob)->age;

		$tabData = [
			'serial_no'							=>	$form_data->caseData->NV_SERIAL_NO,
			'last_name'							=>	$form_data->client->last_name,
			'other_names'						=>	$form_data->client->other_names,
			'address'							=>	($form_data->client->type == "staff") ? $form_data->client->address : $form_data->client->principal->RESIDENCE,
			'office_phone_no'					=>	($form_data->client->type == "staff") ? $form_data->client->phone : $form_data->client->principal->MOBILE_NO,
			'dob'								=>	($age > 18) ? "Over 18" : $age,
			'dl_held_years'						=>	$form_data->caseData->DRIVING_YEARS,
			'dl_country_issue'					=>	$form_data->caseData->COUNTRY_OF_ISSUE,
			'current_dl_class'					=>	$form_data->caseData->DL_CLASSES,
			'cert_competency_no'				=>	$form_data->caseData->COMPETENCY_NO,
			'dl_convicted'						=>	ucwords(strtoupper($form_data->caseData->COURT_CONVICTION)),
			'dl_disqualified'					=>	ucwords(strtoupper($form_data->caseData->COURT_DISQUALIFICATION)),
			'epileptic_other_attacks'			=>	ucwords(strtoupper($form_data->caseData->EPILEPTIC_ATTACK)),
			'read_regt_no_22m'					=>	ucwords(strtoupper($form_data->caseData->EYESIGHT)),
			'missing_hand_foot'					=>	ucwords(strtoupper($form_data->caseData->HAND_FOOT)),
			'missing_hand_foot_details'			=>	$form_data->caseData->HAND_FOOT_DETAILS,
			'disease_impaired_driving'			=>	ucwords(strtoupper($form_data->caseData->MENTAL_HEALTH)),
			'disease_impaired_driving_details'	=>	$form_data->caseData->MENTAL_HEALTH_DETAILS,
			'today'								=>	$date
		];

		return $tabData;
	}
}