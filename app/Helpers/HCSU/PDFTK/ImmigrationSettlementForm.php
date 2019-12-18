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
			
		];

		// $template = new \App\Helpers\HCSU\PDFTK\Templates\VISAExtensionForm($tabData);
        $this->filename = "{$document->form_name} for {$visa_data->client->name} - {$date}";
        return $tabData;
	}
}