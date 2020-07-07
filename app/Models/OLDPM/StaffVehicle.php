<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class StaffVehicle extends Model
{
    protected $connection = "old_pm";
    protected $table = "unon_sm_vehicle";
    protected $primaryKey = "record_id";

    public function staff(){
    	return $this->belongsTo(\App\Models\OLDPM\StaffMember::class, "index_no", "index_no");
    }
}