<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VATInvoice extends Model
{
    protected $connection = "pm_data";
    protected $table = "VAT_01_INVOICES";

    public function vat(){
    	return $this->belongsTo('\App\Models\VAT', 'UID', 'REF_ID');
    }
}
