<?php

namespace App\Helpers\HCSU\PDFTK;

class VISAExtensionForm{
	private $filename;

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document){
		$visa_data = \App\Helpers\HCSU\Data\VISAExtensionData::get($case_no);
		$name = $filename = "";

		$date = date('F d, Y');

		$tabData = [
			"other_names"		=>	$visa_data->client->clientdata->OTHER_NAMES,
			"nationality"		=>	$visa_data->client->nationality,
			"date_of_issue"		=>	$visa_data->client->passport->issue_date,
			"place_of_issue"	=>	$visa_data->client->passport->place_of_issue,
			"address_in_kenya"	=>	$visa_data->case_data->KENYA_ADDRESS,
			"passport_no"		=>	$visa_data->client->passport->passport_no,
			"surname"			=>	strtoupper($visa_data->client->clientdata->LAST_NAME),
			"port_of_entry"		=>	$visa_data->case_data->PORT_OF_ENTRY,
			"extending_reason"	=>	$visa_data->case_data->EXTENDING_REASON,
			"date_of_entry"		=>	$visa_data->case_data->DATE_OF_ENTRY,
			"extending_period"	=>	"{$visa_data->case_data->PERIOD} {$visa_data->case_data->PERIOD_UNITS}"
		];

		// $template = new \App\Helpers\HCSU\PDFTK\Templates\VISAExtensionForm($tabData);
        $this->filename = "{$document->form_name} for {$visa_data->client->name} - {$date}";
        return $tabData;
	}
}