<?php

namespace App\Helpers\HCSU\PDFTK;

class Pro1C{
	private $filename;

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document){
		$form_data = \App\Helpers\HCSU\Data\Pro1CData::get($case_no);

		$name_title = "";

		if ($form_data->client->type == "staff") {
			$name_title = "{$form_data->client->fullname}, {$form_data->client->designation}";
		}else{
			$name_title = "{$form_data->client->fullname}";
		}
		
		$tabData = [
			'serial_no'				=>	$form_data->caseData->NV_SERIAL_NO,
			'agency'				=>	$form_data->client->organization,
			'today'					=>	date('F d, Y'),
			'name_title'			=>	$name_title,
			'yom'					=>	$form_data->vehicle->yom,
			'purchase_date'			=>	$form_data->vehicle->purchaseDate,
			'registration_date'		=>	$form_data->vehicle->dateOfRegistration,
			'engine_no'				=>	$form_data->vehicle->engine_no,
			'chassis_no'			=>	$form_data->vehicle->chassis_no,
			'disposal_reason'		=>	$form_data->caseData->REASON_FOR_DISPOSAL,
			'buyer_name_address'	=>	($form_data->caseData->BUYER_IDENTIFIED) ? "{$form_data->caseData->BUYER_FULL_NAMES}, {$form_data->caseData->BUYER_ADDRESS}" : "Ownership remains with applicant",
			'sale_price'			=>	($form_data->caseData->BUYER_IDENTIFIED && $form_data->caseData->SALE_PRICE) ? $form_data->caseData->SALE_CURRENCY ." " . number_format($form_data->caseData->SALE_PRICE) : "Not Yet Determined",
			'previously_disposed_other_vehicles'	=>	'None'
		];

		return $tabData;
	}
}