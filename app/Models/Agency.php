<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $connection = "2019";

    protected $fillable = ["HOST_COUNTRY_ID", "ACRONYM", "AGENCYNAME", "POBOX", "LOCATION", "PHY_ADDRESS", "HCA", "PIN_NO", "POSTCODE", "DATE_ACCREDITED", "EMAIL", "OFFICE_NO", "FOCAL_POINT", "FP_EMAIL", "FP_MOBILE_NO"];
    protected $primaryKey = "AGENCY_ID";
    
    public function focalPoints(){
    	return $this->hasMany('App\Models\AgencyFocalPoint', 'AGENCY_HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
    }

    public function applications(){
    	
    }
}
