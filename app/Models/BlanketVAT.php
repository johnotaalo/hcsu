<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlanketVAT extends Model
{
    protected $connection = "pm_data";
    protected $table = "VAT_02";

    function supplier(){
    	return $this->hasOne('\App\Models\BlanketSupplier', 'BV_S_ID', 'SERVICE_PROVIDER');
    }
}
