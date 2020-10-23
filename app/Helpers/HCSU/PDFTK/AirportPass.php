<?php

namespace App\Helpers\HCSU\PDFTK;

class AirportPass{
	private $filename = "Airport Pass Form";

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document, $extraParams = null){
		$form_data = \App\Helpers\HCSU\Data\AirportPassData::get($case_no);

		$tabData = [
			'serial_no'					=>	$form_data->ref,
			'employee_name'				=>	$form_data->client->name,
			'employee_organization'		=>	$form_data->client->organization,
			'employee_postal_address'	=>	$form_data->client->principal->ADDRESS,
			'employee_designation'		=>	$form_data->client->designation,
			'duration'					=>	"1 Year",
			'areas'						=>	'apron_lounges',
			'identification'			=>	$form_data->client->passport,
			'doa'						=>	$form_data->client->doa,
			'r_number'					=>	($form_data->client->principal->R_NO != "" || $form_data->client->principal->R_NO != "null" || !is_null($form_data->client->principal->R_NO)) ? $form_data->client->principal->R_NO : "",
			'applicant_date'			=>	$form_data->date
		];

		return $tabData;
	}
}