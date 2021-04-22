<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormA extends Model
{
	protected $connection = "pm_data";
    protected $table = "VSR_01";

    protected $appends = ["client_details"];

    protected $fillable = ["CASE_NO", "PRO_1B_CASE_NO", 'HOST_COUNTRY_ID','SERIAL_NO','DUTY_PAID','PLATE_TYPE','INSURANCE_COMPANY','POLICY_NO','USE_ROAD','USE_ESTATE','USE_TOWN','USE_DISTRICT','MANAGER_APPROVAL','SUBMIT_TO_MOFA','MOFA_APPROVAL','PLATE_NO'];

    public function getClientDetailsAttribute(){
    	$clientType = identify_hcsu_client($this->attributes['HOST_COUNTRY_ID']);
    	$host_country_id = $this->attributes['HOST_COUNTRY_ID'];

    	$clientObj = new \StdClass;

    	$clientObj->type = $clientType;

    	if ($clientObj->type == "staff") {
    		$principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $host_country_id)->first();

    		$name = format_other_names($principal->OTHER_NAMES) . " " . strtoupper($principal->LAST_NAME);
			$client_name = $name;

    		$clientObj->name = $client_name;
    	}else if($clientObj->type == "agency"){
    		$agency = \App\Models\Agency::where('HOST_COUNTRY_ID', $host_country_id)->first();
			$clientObj->name = $agency->ACRONYM;

    	}else if($clientObj->type == "dependent"){
			$dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $host_country_id)->first();

			$relationship = $dependent->relationship->RELATIONSHIP;
			$relationship = ($relationship == "Spouse") ? strtolower($relationship) : "dependent";

			$name = format_other_names($dependent->OTHER_NAMES) . " " . strtoupper($dependent->LAST_NAME);
			$client_name = "{$name} {$dependent->relationship->RELATIONSHIP} of {$dependent->principal->fullname}";

			$clientObj->name = $client_name;
		}

		return $clientObj;
    }

    public function vehicle(){
    	return $this->belongsTo(\App\Models\Pro1BVehicles::class, 'PRO_1B_CASE_NO', 'CASE_NO');
    }
}
