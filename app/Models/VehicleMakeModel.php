<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleMakeModel extends Model
{
    protected $connection = "2019";
    protected $table = "ref_make_model";
    public $timestamps = false;
    protected $primaryKey = "MAKE_MODEL_ID";
}
