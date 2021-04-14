<?php

namespace App\Helpers\HCSU\PDFTK;

class Revalidation{
	private $filename;

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document){
		$revalidationData = \App\Models\Revalidation::where('CASE_NO', $case_no)->first();

		$tabData = [];

		if ($revalidationData->INITIAL_SYSTEM == "new") {
			if ($revalidationData->APPLICATION_TYPE == "pro-1a") {
				$tabData = Pro1A::getData($revalidationData->INITIAL_CASE_NO, $document);
			}else if ($revalidationData->APPLICATION_TYPE == "pro-1b") {
				$clientType = identify_hcsu_client($revalidationData->HOST_COUNTRY_ID);
				if($clientType == "agency"){
					$tabData = Pro1BAgency::getData($revalidationData->INITIAL_CASE_NO, $document);
				}else{
					$tabData = Pro1BStaff::getData($revalidationData->INITIAL_CASE_NO, $document);
				}
			}else if ($revalidationData->APPLICATION_TYPE == "pro-1c") {
				$tabData = Pro1C::getData($revalidationData->INITIAL_CASE_NO, $document);
			}
		}

		return $tabData;
	}
}