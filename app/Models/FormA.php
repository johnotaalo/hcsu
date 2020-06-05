<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormA extends Model
{
	protected $connection = "pm_data";
    protected $table = "VSR_01";

    protected function vehicle(){
    	return $this->belongsTo(\App\Models\Pro1BVehicles::class, 'PRO_1B_CASE_NO', 'CASE_NO');
    }
}
