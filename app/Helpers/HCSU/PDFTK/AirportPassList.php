<?php

namespace App\Helpers\HCSU\PDFTK;

class AirportPassList{
	private $filename = "Airport Pass List";

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document, $extraParams = null){
		$form_data = \App\Helpers\HCSU\Data\AirportPassData::get($case_no);

		$tabData = [
			'client_name'				=>	$form_data->client->name,
			'client_organization'		=>	$form_data->client->organization,
			'client_id_no'				=>	$form_data->client->passport,
			'client_designation'		=>	$form_data->client->designation,
			'client_previous_pass_no'	=>	$form_data->actual->PREVIOUS_PASS,
			'client_mobile_no'			=>	$form_data->client->contact,
			'client_nationality'		=>	$form_data->client->nationality
		];

		return $tabData;
	}
}