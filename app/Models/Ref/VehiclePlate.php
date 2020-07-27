<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class VehiclePlate extends Model
{
    protected $connection = "2019";

    protected $fillable = [
    	"prefix_id",
    	"plate_number",
    	"suffix",
    	"status"
    ];

    protected $appends = ['complete_number'];

    public function prefix(){
    	return $this->belongsTo(\App\Models\Ref\VehiclePlatePrefix::class, 'prefix_id');
    }

    public function client(){
        return $this->hasOne(\App\Models\VehicleOwner::class, "REG_PLATE_ID");
    }

    public function getCompleteNumberAttribute(){
        $prefix = $this->prefix->prefix;
        $number = $prefix . $this->plate_number;

        return $number;
    }
}
