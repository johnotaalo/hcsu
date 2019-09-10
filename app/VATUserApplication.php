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
}
