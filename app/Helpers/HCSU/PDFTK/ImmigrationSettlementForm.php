<?php

namespace App\Helpers\HCSU\PDFTK;

class ImmigrationSettlementForm{
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
			"Application"					=>	$visa_data->case_data->IS_APPLICATION_FOR,
			'client_name'					=>	"{$visa_data->client->clientdata->LAST_NAME} {$visa_data->client->clientdata->OTHER_NAMES}",
			"nationality"					=>	$visa_data->client->nationality,
			"date_of_birth"					=>	$visa_data->client->clientdata->DATE_OF_BIRTH,
			"place_of_birth"				=>	$visa_data->client->clientdata->PLACE_OF_BIRTH,
			"occupation"					=>	$visa_data->case_data->IS_OCCUPATION,
			"address"						=>	$visa_data->case_data->IS_ADDRESS_IN_COUNTRY_OF_RESIDENCE,
			"passport_no"					=>	$visa_data->client->passport->passport_no,
			"issue_date"					=>	$visa_data->client->passport->issue_date,
			"place_of_issue"				=>	$visa_data->client->passport->place_of_issue,
			"reasons_for_visiting"			=>	$visa_data->case_data->IS_REASONS_FOR_VISITING,
			"date_of_arrival"				=>	$visa_data->case_data->IS_DATE_OF_ARRIVAL,
			"duration"						=>	"{$visa_data->case_data->IS_DURATION} {$visa_data->case_data->IS_DURATION_UNITS}",
			"kenyan_address"				=>	$visa_data->case_data->IS_PHYSICAL_ADDRESS,
			"particulars_of_existing_pass"	=>	$visa_data->case_data->IS_PERMIT_ISSUED,
			"date"							=>	date("d/m/Y")
		];

		// dd($tabData);

		foreach ($tabData as $key => $data) {
			if($key != 'reasons_for_visiting' && $key != 'occupation' && $key != "Application")
				$data = strtoupper($data);
			if($key == "address" || $key == "kenyan_address")
				$data = ucwords(strtolower($data));

			$tabData[$key] = $data;
		}

		// $template = new \App\Helpers\HCSU\PDFTK\Templates\VISAExtensionForm($tabData);
        $this->filename = "{$document->form_name} for {$visa_data->client->name} - {$date}";
        return $tabData;
	}
}