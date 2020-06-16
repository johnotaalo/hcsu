<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    protected $connection = 'pm_data';

    protected $table = "VSR_03";

    public function forma(){
    	return $this->belongsTo(\App\Models\FormA::class, 'FORM_A_CASE_NO', 'CASE_NO');
    }
}
