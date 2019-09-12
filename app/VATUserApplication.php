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

    protected $appends = array('supplier', 'data');

    protected function getDataAttribute(){
    	return \App\Helpers\HCSU\Data\VATData::get($this->CASE_NO);
    }

    protected function getSupplierAttribute(){
    	$vat = \App\Models\VAT::where('CASE_NO', $this->CASE_NO)->first();

    	return $vat->supplier;
    }
}
