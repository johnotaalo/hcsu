<?php

namespace App\Helpers\HCSU\Data;

use \App\Models\Revalidation;

class RevalidationData{
	public static function get($case_no){
		$caseData = Revalidation::where('CASE_NO', $case_no)->first();

		$data = new \StdClass;
		$clientObj = new \StdClass;
		$clientType = identify_hcsu_client($caseData->HOST_COUNTRY_ID);
		$clientObj->type = $clientType;

		if ($clientObj->type == "staff") {

		}else if($clientObj->type == "agency"){

		}else if($clientObj->type == "dependent"){
		}

		$data->client = $clientObj;
		$data->case_no = $case_no;
		$data->ref = $caseData->NV_SERIAL_NO;
		$data->date = date('F d, Y');

		return $data;
	}
}