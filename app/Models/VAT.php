<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VAT extends Model
{
    protected $connection = "pm_data";
    protected $table = "VAT_01";

    function invoices(){
    	return $this->hasMany('\App\Models\VATInvoice', 'REF_ID', 'UID');
    }

    function supplier(){
    	return $this->hasOne('\App\Models\Supplier', 'ID', 'SUPPLIER_ID');
    }
}
