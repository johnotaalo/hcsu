<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleOwner extends Model
{
    protected $table = "VEHICLE_OWNER";
	protected $primaryKey = "ID";
	protected $fillable = ["HOST_COUNTRY_ID", "REG_PLATE_ID", "STATUS", "VEHICLE_ID", "LOG_BOOK_NO"];
}
