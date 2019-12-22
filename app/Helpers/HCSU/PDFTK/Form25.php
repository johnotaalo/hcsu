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

		$tabData = [
			"other_names"		=>	$form_data->client->clientdata->OTHER_NAMES,
			"nationality"		=>	$form_data->client->nationality,
			"date_of_issue"		=>	$form_data->client->passport->issue_date,
			"place_of_issue"	=>	$form_data->client->passport->place_of_issue,
			"address_in_kenya"	=>	$form_data->case_data->KENYA_ADDRESS,
			"passport_no"		=>	$form_data->client->passport->passport_no,
			"surname"			=>	strtoupper($form_data->client->clientdata->LAST_NAME),
			"port_of_entry"		=>	$form_data->case_data->PORT_OF_ENTRY,
			"extending_reason"	=>	$form_data->case_data->EXTENDING_REASON,
			"date_of_entry"		=>	$form_data->case_data->DATE_OF_ENTRY,
			"extending_period"	=>	"{$form_data->case_data->PERIOD} {$form_data->case_data->PERIOD_UNITS}"
		];

		// $template = new \App\Helpers\HCSU\PDFTK\Templates\VISAExtensionForm($tabData);
        $this->filename = "{$document->form_name} for {$form_data->client->name} - {$date}";
        return $tabData;
	}
}