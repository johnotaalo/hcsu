<?php

namespace App\Helpers\HCSU\PDFTK;

class AirportPass{
	private $filename = "Airport Pass Form";

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document, $extraParams = null){
		$form_data = \App\Helpers\HCSU\Data\AirportPassData::get($case_no);
	}
}