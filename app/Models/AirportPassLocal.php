<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AirportPassLocal extends Model
{
    protected $connection = "pm_data";
    protected $table = "AP_01_LOCALS";
}
