<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FireArmsApplication extends Model
{
    protected $connection = "pm_data";
    protected $table = "FP_01";
    protected $appends = ["staff_details"];

    public function getStaffDetailsAttribute(){
        return collect(json_decode($this->attributes['STAFF_MEMBER_DETAILS']))->toArray();
    }
}
