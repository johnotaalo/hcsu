<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class VehiclePlatePrefixAgency extends Model
{
	// use SoftDeletes;
    protected $connection = '2019';
    protected $table = 'vehicle_plate_prefix_agencies';
    protected $fillable = ['prefix_id', 'host_country_id'];

    protected $appends = ['agency_data'];

    public function prefix(){
    	return $this->belongsTo(\App\Models\Ref\VehiclePlatePrefix::class, "prefix_id");
    }

    public function agency(){
    	return $this->belongsTo(\App\Models\Agency::class, "host_country_id", "host_country_id");
    }

    public function getAgencyDataAttribute(){
    	return \App\Models\Agency::where('host_country_id', $this->host_country_id)->first();
    }

    // public function getAgencyNameAttribute(){
    // 	return \App\Models\Agenc
    // }
}
