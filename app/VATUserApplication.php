<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VATUserApplication extends Model
{
	protected $connection = "pm_data";
	protected $table = "VAT_01_USER_APPLICATION";
    protected $fillable = [
    	'USER_ID',
		'ACKNOWLEDGEMENT_LINK',
		'CASE_NO',
		'APPROVED',
		'APPROVAL_COMMENT',
		'USER_CLAIMED',
		'CLAIMED_AT',
		'STATUS'
    ];

    protected $appends = array('supplier', 'data', 'claimer_data', 'invoices');

    protected function getDataAttribute(){
    	return \App\Helpers\HCSU\Data\VATData::get($this->CASE_NO);
    }

    protected function getSupplierAttribute(){
    	$vat = \App\Models\VAT::where('CASE_NO', $this->CASE_NO)->first();

        if($vat)
    	   return $vat->supplier;
        else
            return null;
    }

    protected function user(){
        return $this->belongsTo(\App\User::class, "USER_ID");
    }

    protected function claimer(){
        return $this->belongsTo(\App\Models\PM\User::class, "USER_CLAIMED", "USR_UID");
    }

    public function documents(){
        return $this->hasMany(\App\VATUserApplicationDocument::class, "APPLICATION_ID");
    }

    public function getInvoicesAttribute(){
        $vat = \App\Models\VAT::where('CASE_NO', $this->CASE_NO)->first();
        return $vat->invoices;
    }

    protected function getClaimerDataAttribute(){
        return \App\Models\PM\User::where("USR_UID", $this->USER_CLAIMED)->first();
    }
}
