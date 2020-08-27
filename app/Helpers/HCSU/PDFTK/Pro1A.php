<?php

namespace App\Helpers\HCSU\PDFTK;

class Pro1A{
	private $filename;

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document){
		$form_data = \App\Helpers\HCSU\Data\Pro1AData::get($case_no);
		$date = date('F d, Y');
		
		$tabData = [
			'serial_no'				=>	$form_data->caseData->NV_SERIAL_NO,
			'agency'				=>	$form_data->client->organization,
			'today'					=>	date('F d, Y'),
			'fullname'				=>	$form_data->client->fullname,
			'designation'			=>	($form_data->client->type != "agency") ? $form_data->client->designation : "",
			'cl_agent_name'			=>	$form_data->caseData->clearing_agent->CLEARING_AGENT_NAME,
			'cl_agent_address'		=>	$form_data->caseData->clearing_agent->CLEARING_AGENT_ADDRESS,
			'spirits'				=>	($form_data->caseData->SPIRITS != 0) ? "{$form_data->caseData->SPIRITS} Pieces" : "N/A",
			'wine'					=>	($form_data->caseData->WINES != 0) ? "{$form_data->caseData->WINES} Pieces" : "N/A",
			'beer'					=>	($form_data->caseData->BEERS != 0) ? "{$form_data->caseData->BEERS} Pieces" : "N/A",
			'tobacco'				=>	($form_data->caseData->TOBACCO != 0) ? "{$form_data->caseData->TOBACCO} Pieces" : "N/A",
			'awb_bl_invoice'		=>	"BL/AWB No: {$form_data->caseData->AIRWAY_BILL_NO}; Invoice No: {$form_data->caseData->INVOICE_NO}; Port of Clearance: {$form_data->caseData->port->PORT_OF_CLEARANCE}",
			'last_application_date'	=>	'N/A',
			'port_of_clearance'		=>	$form_data->caseData->port->PORT_OF_CLEARANCE
		];

		$this->filename = "{$document->form_name} for {$form_data->client->name} - {$date}";

		return $tabData;
	}
}