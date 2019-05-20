<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrincipalPassport extends Model
{
	protected $primaryKey = "ID";
	protected $table = "PRINCIPAL_PASSPORT";
	protected $appends = ["type"];
    protected $fillable = ["PRINCIPAL_ID", "PASSPORT_TYPE_ID", "PASSPORT_NO", "PLACE_OF_ISSUE", "COUNTRY_OF_ISSUE", "ISSUE_DATE", "EXPIRY_DATE"];

    public function passport_type(){
    	return $this->belongsTo('\App\Models\PassportType', 'PASSPORT_TYPE_ID', 'ID');
    }

    public function getTypeAttribute(){
    	$passportType = $this->passport_type;

    	return $passportType;
    }
}
