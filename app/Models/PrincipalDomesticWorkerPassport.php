<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrincipalDomesticWorkerPassport extends Model
{
    protected $table = "PRINCIPAL_DOMESTIC_WORKER_PASSPORTS";
    protected $fillable = ['HOST_COUNTRY_ID', 'PASSPORT_NO', 'PLACE_OF_ISSUE', 'PASSPORT_TYPE', 'COUNTRY_OF_ISSUE', 'ISSUE_DATE', 'EXPIRY_DATE'];
    protected $appends = ['actual_country_of_issue'];

    public function getActualCountryOfIssueAttribute(){
    	// dd($this->attr['PLACE_OF_ISSUE']);
    	return \App\Models\Country::where('iso_3', $this->COUNTRY_OF_ISSUE)->first();
    }
}
