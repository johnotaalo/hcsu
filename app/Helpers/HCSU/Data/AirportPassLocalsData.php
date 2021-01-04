<?php

namespace App\Helpers\HCSU\Data;

use \App\Models\AirportPassLocal;

class AirportPassLocalsData{
	public static function get($case_no, $type = "new"){
		$data = new \StdClass;
		$clientObj = new \StdClass;

		$airportPassData = AirportPassLocal::where('CASE_NO', $case_no)->first();

		$mission = $airportPassData->CLIENT_ORGANIZATION;

		$clientObj->name = strtoupper($airportPassData->CLIENT_LAST_NAME) . ", " . $airportPassData->CLIENT_OTHER_NAMES;
		$clientObj->organization = $mission;

		$data->client = $clientObj;
		$data->case_no = $case_no;
        $data->ref = $airportPassData->NV_SERIAL_NO;
        $data->date = date('F d, Y');
        $data->type = ($airportPassData->APPLICATION_TYPE == "new") ? "New Application" : "Renewal";
        $data->actual = $airportPassData;

		return $data;
	}
}