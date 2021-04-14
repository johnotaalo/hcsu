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
			$class = new \StdClass;
			if ($revalidationData->APPLICATION_TYPE == "pro-1a") {
				$class = new Pro1A();
			}else if ($revalidationData->APPLICATION_TYPE == "pro-1b") {
				$clientType = identify_hcsu_client($revalidationData->HOST_COUNTRY_ID);
				if($clientType == "agency"){
					$class = new Pro1BAgency();
				}else{
					$class = new Pro1BStaff();
				}
			}else if ($revalidationData->APPLICATION_TYPE == "pro-1c") {
				$class = new Pro1C();
			}

			$tabData = $class->getData($revalidationData->INITIAL_CASE_NO, $document);
		}

		return $tabData;
	}
}