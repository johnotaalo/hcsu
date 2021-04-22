<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class StaffVehicle extends Model
{
    protected $connection = "old_pm";
    protected $table = "unon_sm_vehicle";
    protected $primaryKey = "record_id";

    protected $appends = ['new_make_model', 'new_color'];

    public function staff(){
    	return $this->belongsTo(\App\Models\OLDPM\StaffMember::class, "index_no", "index_no");
    }

    public function getNewMakeModelAttribute(){
    	$make_model = $this->attributes['make_model'];

    	$new_make_model = \App\Models\VehicleMakeModel::where('MAKE_MODEL', $make_model)->first();

    	return $new_make_model;
    }

    public function getNewColorAttribute(){
    	$color = $this->attributes['color'];

    	$new_color = \App\Models\VehicleColor::where('COLOUR', $color)->first();

    	return $new_color;
    }
}