<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlanketVAT extends Model
{
    protected $connection = "pm_data";
    protected $table = "VAT_02";

    protected $appends = ['ipmis_log'];

    function supplier(){
    	return $this->hasOne('\App\Models\BlanketSupplier', 'BV_S_ID', 'SERVICE_PROVIDER');
    }

    function batch(){
    	return $this->belongsTo(\App\Models\BlanketVATBatch::class, "BATCH_ID");
    }

    function getIpmisLogAttribute(){
    	return \App\Models\IPMISLog::where(['IPMIS_NO'	=>	$this->IPMIS_NUMBER, 'CASE_NO'	=>	$this->CASE_NO])->first();
    }
}
