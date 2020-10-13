<?php

namespace App\Helpers\HCSU\PDFTK;

class Form11{
	private $filename = "Form XI";

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document, $extraParams = null){
		$form_data = \App\Helpers\HCSU\Data\DLDuplicate::get($case_no);

		$date = date("F d, Y");

		$tabData = [
			'serial_no'						=>	$form_data->caseData->NV_SERIAL_NO,
			'today'							=>	$date,
			'fullname'						=>	$form_data->client->name,
			'address'						=>	"P.O. BOX 47074 - 00100, NAIROBI",
			'dl_no_to_duplicate0'			=>	$form_data->caseData->DL_NO,
			'dl_no_to_duplicate'			=>	$form_data->caseData->DL_NO,
			'dl_issue_date'					=>	$form_data->caseData->ISSUE_DATE,
			'dl_loss_defacement_details'	=>	$form_data->caseData->DETAILS
		];

		return $tabData;
	}
}