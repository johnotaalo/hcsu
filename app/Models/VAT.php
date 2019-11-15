<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VAT extends Model
{
    protected $connection = "pm_data";
    protected $table = "VAT_01";

    protected $appends = ["total_amount"];

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
}
