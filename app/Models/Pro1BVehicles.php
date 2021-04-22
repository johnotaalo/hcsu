<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro1BVehicles extends Model
{
	protected $connection = "pm_data";

    protected $table = "DF_02_Vehicles";

    protected $fillable = ["CASE_NO", "ENGINE_NO", "CHASSIS_NO", "MAKE_MODEL_ID", "COLOR_ID", "YOM", "FUEL", "RATING", "VEHICLE_CATEGORY", "VEHICLE_TYPE", "BODY_TYPE", "VEHICLE_WEIGHT", "VEHICLE_VALUE", "COUNTRY_OF_ORIGIN", "ORIGINAL_REGISTRATION", "VEHICLE_SEATING", "CURRENCY", "VEHICLE_CARRYING", "FORM_A_CASE_NO"];

    public function make(){
    	return $this->belongsTo(\App\Models\VehicleMakeModel::class, 'MAKE_MODEL_ID', 'MAKE_MODEL_ID');
    }

    public function type(){
    	return $this->belongsTo(\App\Models\VehicleType::class, 'VEHICLE_TYPE', 'ID');
    }

    public function body(){
    	return $this->belongsTo(\App\Models\VehicleBody::class, 'BODY_TYPE', 'ID');
    }

    public function color(){
    	return $this->belongsTo(\App\Models\VehicleColor::class, 'COLOR_ID', 'ID');
    }

    public function country(){
    	return $this->belongsTo(\App\Models\Country::class, 'COUNTRY_OF_ORIGIN', 'id');
    }
}
