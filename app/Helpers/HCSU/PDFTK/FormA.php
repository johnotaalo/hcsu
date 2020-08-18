<?php

namespace App\Helpers\HCSU\PDFTK;

class FormA{
	private $filename;

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document, $extraParams = null){
		$form_data = \App\Helpers\HCSU\Data\FormAData::get($case_no);

		$typeArray = $chassisArray = $engineArray = $colorArray = $neworOld = $previouslyRegistered = $method = [];

		switch ($form_data->caseData->vehicle->type->VEH_TYPE) {
			case 'Motor Vehicle':
				$typeArray['vehicle_type_motor'] = 'X';
				break;
			case 'Motorcycle':
				$typeArray['vehicle_type_bike'] = 'X';
				break;
			case 'Trailer':
				$typeArray['vehicle_type_trailer'] = 'X';
				break;
		}

		if($form_data->caseData->vehicle->VEHICLE_CATEGORY == "Used"){
			$neworOld['new_vehicle_no'] = 'X';
			$previouslyRegistered['previously_registered_yes'] = 'X';
			$previouslyRegistered['previously_registered_country'] = $form_data->caseData->vehicle->country->name;
			$previouslyRegistered['previous_registration_no'] = $form_data->caseData->vehicle->ORIGINAL_REGISTRATION;
		}else{
			$neworOld['new_vehicle_yes'] = 'X';
			$previouslyRegistered['previously_registered_no'] = 'X';
		}

		if($form_data->caseData->vehicle->FUEL == 1){
			$method['method_of_propulsion_diesel'] = 'X';
		}elseif($form_data->caseData->vehicle->FUEL == 2){
			$method['method_of_propulsion_petrol'] = 'X';
		}elseif($form_data->caseData->vehicle->FUEL == 3){
			$method['method_of_propulsion_electric'] = 'X';
		}

		$color = $form_data->caseData->vehicle->color;
		if ($color->CODE == 'WHI' || $color->CODE == 'CRM') {
			$colorArray['body_colour_white'] = 'X';
		}elseif ($color->CODE == 'ROS' || $color->CODE == 'CLA' || $color->CODE == 'RED'|| $color->CODE == 'MAR'|| $color->CODE == 'PIN') {
			$colorArray['body_colour_red'] = 'X';
		}elseif ($color->CODE == 'RUS' || $color->CODE == 'ORA') {
			$colorArray['body_colour_orange'] = 'X';
		}elseif ($color->CODE == 'NAV' || $color->CODE == 'BLU' || $color->CODE == 'TUR') {
			$colorArray['body_colour_blue'] = 'X';
		}elseif ($color->CODE == 'GRE') {
			$colorArray['body_colour_green'] = 'X';
		}elseif ($color->CODE == 'YEL' || $color->CODE == 'GOL') {
			$colorArray['body_colour_yellow'] = 'X';
		}elseif ($color->CODE == 'BRO' || $color->CODE == 'BEI' || $color->CODE == 'COP'|| $color->CODE == 'BRZ'|| $color->CODE == 'TAN') {
			$colorArray['body_colour_brown'] = 'X';
		}elseif ($color->CODE == 'BLA') {
			$colorArray['body_colour_black'] = 'X';
		}elseif ($color->CODE == 'GRA' || $color->CODE == 'ALU' || $color->CODE == 'MTL'|| $color->CODE == 'SIL') {
			$colorArray['body_colour_gray'] = 'X';
		}elseif ($color->CODE == 'PUR' || $color->CODE == 'VIO') {
			$colorArray['body_colour_purple'] = 'X';
		}

		$chassis_no = $form_data->caseData->vehicle->CHASSIS_NO;
		$engine_no = $form_data->caseData->vehicle->ENGINE_NO;

		$chassisFrags = str_split($chassis_no, 2);
		$engineFrags = str_split($engine_no, 2);

		foreach ($chassisFrags as $key => $value) {
			$no = $key + 1;
			$chassisArray["chassis_no_" . $no] = $value;
		}

		foreach ($engineFrags as $key => $value) {
			$no = $key + 1;
			$engineArray["engine_no_" . $no] = $value;
		}

		$address = "";
		$mobile_no = "";

		if ($form_data->client->type == "staff") {
			$mobile_no = $form_data->client->fullObj->MOBILE_NO;
			$address = $form_data->client->fullObj->ADDRESS;
		}else if ($form_data->client->type == "dependent"){
			$mobile_no = $form_data->client->principal->MOBILE_NO;
			$address = $form_data->client->principal->ADDRESS;
		}else {
			$mobile_no = $form_data->agency->OFFICE_NO;
			$address = $form_data->agency->POBOX . " " . $form_data->agency->POSTCODE;
		}

		$tabData = [
			'serial_no'						=>	$form_data->caseData->SERIAL_NO,
			'start_month'					=>	date('F'),
			'start_year'					=>	date('y'),
			'duration12'					=>	'X',
			'pdf_insurance_company'			=>	$form_data->caseData->INSURANCE_COMPANY,
			'make'							=>	$form_data->caseData->vehicle->make->MAKE_MODEL,
			'body_type'						=>	$form_data->caseData->vehicle->body->BODY_TYPE,
			'yom'							=>	$form_data->caseData->vehicle->YOM,
			'tare_weight'					=>	$form_data->caseData->vehicle->VEHICLE_WEIGHT,
			'number_of_axles'				=>	2,
			'vehicle_value'					=>	"{$form_data->caseData->vehicle->CURRENCY} {$form_data->caseData->vehicle->VEHICLE_VALUE}",
			'carrying_capacity_kg'			=>	$form_data->caseData->vehicle->VEHICLE_CARRYING,
			'carrying_capacity_seats'		=>	$form_data->caseData->vehicle->VEHICLE_SEATING,
			'vehicle_location_road'			=>	$form_data->caseData->USE_ROAD,
			'vehicle_location_estate'		=>	$form_data->caseData->USE_ESTATE,
			'vehicle_location_town'			=>	$form_data->caseData->USE_TOWN,
			'vehicle_location_district'		=>	$form_data->caseData->USE_DISTRICT,
			'rating'						=>	$form_data->caseData->vehicle->RATING,
			'vehicle_use_private'			=>	'X',
			'fullname'						=>	$form_data->client->name,
			'designation'					=>	$form_data->client->designation,
			'agency'						=>	$form_data->client->organization,
			'office_phone_no'				=>	$mobile_no,
			'address'						=>	$address,
			'date_dm'						=>	date('d/m'),
			'date_y'						=>	date('y')
		];

		$tabData = $tabData + $typeArray + $chassisArray + $engineArray + $colorArray + $neworOld + $previouslyRegistered + $method;

		return $tabData;
	}
}