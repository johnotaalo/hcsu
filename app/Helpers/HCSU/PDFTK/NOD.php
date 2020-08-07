<?php

namespace App\Helpers\HCSU\PDFTK;

class NOD{
	private $filename;

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no){
		$form_data = \App\Helpers\HCSU\Data\NODData::get($case_no);
		// dd($form_data);

		$tabData = [
			'fullname'		=>	$form_data->client->name,
			'mission'		=>	$form_data->client->organization,
			'designation'	=>	$form_data->client->designation,
			'passportno'	=>	$form_data->client->passport->PASSPORT_NO,
			'doa'			=>	$form_data->client->arrival->ARRIVAL,
			'dot'			=>	$form_data->caseData->DATE_OF_DEPARTURE,
			'newaddress'	=>	$form_data->caseData->NEW_ADDRESS,
			'currdate'		=>	$form_data->date
		];

		return $tabData;
	}
}