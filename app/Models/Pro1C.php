<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro1C extends Model
{
	protected $connection = "pm_data";
    protected $table = "DF_03";

    public function vehicle(){
    	if ($this->VEHICLE_DATA_ORIGIN == "old") {
    		return $this->hasOne(\App\Models\OLDPM\StaffVehicle::class, "record_id", "VEHICLE_ID");
    	}
    }
}
