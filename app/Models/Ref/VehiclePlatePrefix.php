<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class VehiclePlatePrefix extends Model
{
    protected $connection = '2019';
    protected $table = 'VEHICLE_PLATE_PREFIX';
    protected $fillable = ['prefix'];

    public function agencies(){
    	return $this->hasMany(\App\Models\Ref\VehiclePlatePrefixAgency::class, 'prefix_id');
    }
}
