<?php

namespace App\Helpers\HCSU\PDFTK;

class Pro1BAgency{
	private $filename;

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document){
		$form_data = \App\Helpers\HCSU\Data\Pro1BData::get($case_no);

		$description = ($form_data->caseData->TYPE_OF_GOODS == "other") ? $form_data->description . "; INVOICE NO: {$form_data->caseData->INVOICE_NO}" : $form_data->description;
		
		$tabData = [
			'serial_no'				=>	$form_data->caseData->NV_SERIAL_NO,
			'agency'				=>	$form_data->client->organization,
			'today'					=>	date('F d, Y'),
			'fullname'				=>	$form_data->client->fullname,
			'designation'			=>	($form_data->client->type != "agency") ? $form_data->client->designation : "",
			// 'doa'					=>	$form_data->client->doa,
			'cl_agent_name'			=>	$form_data->caseData->clearing_agent->CLEARING_AGENT_NAME,
			'cl_agent_address'		=>	$form_data->caseData->clearing_agent->CLEARING_AGENT_ADDRESS,
			'mechandise_desc'		=>	$description,
			'bl_awb'				=>	$form_data->caseData->AIRWAY_BILL_NO,
			'carrier'				=>	$form_data->caseData->INVOICE_NO,
			'clearance_port'		=>	$form_data->caseData->port->PORT_OF_CLEARANCE
		];

		return $tabData;
	}
}