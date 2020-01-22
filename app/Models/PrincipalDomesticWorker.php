<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrincipalDomesticWorker extends Model
{
	protected $table = 'PRINCIPAL_DOMESTIC_WORKER';
	
    protected $fillable = [ 'HOST_COUNTRY_ID', 'PRINCIPAL_ID', 'LAST_NAME', 'OTHER_NAMES', 'ADDRESS', 'EMAIL', 'PHONE_NUMBER', 'PLACE_OF_BIRTH', 'DATE_OF_BIRTH', 'NATIONALITY', 'R_NO', 'PLACE_OF_EMPLOYMENT', 'JOB_TITLE', 'JOB_DESCRIPTION', 'CONTRACT_START_DATE'];

    protected $appends = ['all_passports'];

    public function passports(){
    	return $this->hasMany(\App\Models\PrincipalDomesticWorkerPassport::class, 'HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
    }

    public function getAllPassportsAttribute(){
    	return \App\Models\PrincipalDomesticWorkerPassport::where('HOST_COUNTRY_ID', $this->HOST_COUNTRY_ID)->get();
    }
}
