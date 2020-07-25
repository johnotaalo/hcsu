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
}
