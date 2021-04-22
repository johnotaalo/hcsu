<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

use App\Models\OLDPM\StaffMember;
use App\Models\OLDPM\StaffVehicle;

class FormA extends Model
{
    protected $connection = "old_pm";
    protected $table = "unon_sm_veh_duty_free_regt_approval_application";

    protected $appends = ['vehicle', 'client_details', 'new_fuel', 'new_vehicle_type', 'new_body_type', 'country_of_origin'];

    public function getVehicleAttribute(){
    	return StaffVehicle::where('index_no', $this->attributes['index_no'])->where('chassis_no', $this->attributes['chassis_no'])->where('engine_no', $this->attributes['engine_no'])->first();
    }

    public function getClientDetailsAttribute(){
    	$index_no = $this->attributes['index_no'];

    	// Check if client is an agency.
    	$agency = \DB::connection($this->connection)->table('unon_agency_details_lookup')->where('agencyname', $index_no)->first();

    	if ($agency) {
    		$newPMAgency = \App\Models\Agency::where("ACRONYM", $agency->agencyname)->orWhere("AGENCYNAME", $agency->fullname)->first();
    		return [
    			'name'				=>	$agency->agencyname,
    			'type'				=>	'agency',
    			'HOST_COUNTRY_ID'	=>	($newPMAgency) ? $newPMAgency->HOST_COUNTRY_ID : 0
    		];
    	}else{
    		
    	}
    }

    public function getNewFuelAttribute(){
        return \DB::connection('2019')->table('ref_fuel_type')->where('FUEL', $this->attributes['method_of_propulsion'])->first();
    }

    public function getNewVehicleTypeAttribute(){
        return \App\Models\VehicleType::where('VEH_TYPE', $this->attributes['vehicle_type'])->first();
    }

    public function getNewBodyTypeAttribute(){
        return \App\Models\VehicleBody::where('BODY_TYPE', $this->attributes['body_type'])->first();
    }

    public function getCountryOfOriginAttribute(){
        return \App\Models\Country::where('pm_abbrev', $this->attributes['previously_registered_country'])->first();
    }
}
