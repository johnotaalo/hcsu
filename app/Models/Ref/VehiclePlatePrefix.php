<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class VehiclePlatePrefix extends Model
{
    protected $connection = '2019';
    protected $table = 'VEHICLE_PLATE_PREFIX';
    protected $fillable = ['prefix'];
    protected $appends = ['highest_number'];

    public function agencies(){
    	return $this->hasMany(\App\Models\Ref\VehiclePlatePrefixAgency::class, 'prefix_id');
    }

    public function getHighestNumberAttribute(){
    	$data = \DB::connection('2019')->select("SELECT max(plate_number) as plate_number, suffix FROM `vehicle_plates` WHERE prefix_id = {$this->id} GROUP BY suffix");

    	$cleanedData = collect($data)->map(function($d){
    		return [
    			"plate_number"	=>	(int) rtrim($d->plate_number, $d->suffix),
    			"suffix"		=>	$d->suffix
    		];
    	});

    	return $cleanedData;
    }
}
