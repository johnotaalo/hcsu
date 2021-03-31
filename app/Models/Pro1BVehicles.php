<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro1BVehicles extends Model
{
	protected $connection = "pm_data";

    protected $table = "DF_02_Vehicles";

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
