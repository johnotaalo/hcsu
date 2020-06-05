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
}
