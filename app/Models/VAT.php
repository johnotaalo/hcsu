<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VAT extends Model
{
    protected $connection = "pm_data";
    protected $table = "VAT_01";

    protected $appends = ["total_amount", "application"];

    function invoices(){
    	return $this->hasMany('\App\Models\VATInvoice', 'REF_ID', 'UID');
    }

    function getTotalAmountAttribute(){
    	$invoices = $this->invoices();

    	// dd($invoices);
    }

    function supplier(){
    	return $this->hasOne(\App\Models\Supplier::class, 'ID', 'SUPPLIER_ID');
    }

    function getApplicationAttribute(){
        $data = \DB::connection('pm')->table('APPLICATION')->where("APP_NUMBER", $this->attributes['CASE_NO'])->first();
        if(!$data){
            return null;
        }
        return $data;
        // return $this->belongsTo(\App\Models\PM\Application::class, 'CASE_NO', 'APP_NUMBER');
    }
}
