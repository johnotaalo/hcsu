<?php

namespace App\Helpers\HCSU\PDFTK;

class Form28{
	private $filename;

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document, $extraParams){
		$dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $extraParams['host_country_id'])->first();
		$date = date('F d, Y');

		$this->filename = "{$document->form_name} for {$dependent->LAST_NAME} {$dependent->OTHER_NAMES}- {$date}";
		return [
			'fullname' 			=>	ucwords(strtolower($dependent->principal->OTHER_NAMES)) . " " . strtoupper($dependent->principal->LAST_NAME),
			'mobile'			=>	$dependent->principal->MOBILE_NO,
			'pobox_email'		=>	$dependent->principal->ADDRESS . " {$dependent->principal->EMAIL}",
			'nationality'		=>	$dependent->principal->nationality->official_name,
			'rNo'				=>	'',
			'drelationship'		=>	$dependent->relationship->RELATIONSHIP,
			'depfullname'		=>	ucwords(strtolower($dependent->OTHER_NAMES)) . " " . strtoupper($dependent->LAST_NAME),
			'address'			=>	$dependent->principal->RESIDENCE,
			'dsex'				=>	$dependent->GENDER,
			'dmaritalstatus'	=>	$dependent->GENDER,
			'ddob'				=>	date('Y-m-d', strtotime($dependent->DATE_OF_BIRTH)),
			'dpob'				=>	'',
			'dpptno'			=>	'',
			'ddoi'				=>	'',
			'dpoi'				=>	'',
			'dnationality'		=>	$dependent->COUNTRY
		];
	}
}