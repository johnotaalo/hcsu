<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgencyFocalpointMapping extends Model
{
    protected $connection = "2019";

    protected $fillable = ["FOCAL_POINT_ID", "AGENCY_ID"];

    public function agencies(){
        return $this->hasMany(\App\Models\Agency::class, "AGENCY_ID", "AGENCY_ID");
    }

    public function focalpoints(){
        return $this->hasMany(\App\Models\AgencyFocalPoint::class, "ID", 'FOCAL_POINT_ID');
    }
}
