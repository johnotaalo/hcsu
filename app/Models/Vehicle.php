<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
	protected $table = "VEHICLE";
	protected $primaryKey = "ID";
	protected $fillable = ["ENGINE_NO", "CHASSIS_NO", "COLOR", "MAKE_MODEL_ID"];

	public function makeModel(){
		return $this->belongsTo("\App\Models\VehicleMakeModel", "MAKE_MODEL_ID", "MAKE_MODEL_ID");
	}

	public function owners(){
		return $this->hasMany('\App\Models\VehicleOwner', 'VEHICLE_ID', 'ID');
	}
}
