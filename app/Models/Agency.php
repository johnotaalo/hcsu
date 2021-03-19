<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Agency extends Model
{
    protected $connection = "2019";

    protected $fillable = ["HOST_COUNTRY_ID", "ACRONYM", "AGENCYNAME", "POBOX", "LOCATION", "PHY_ADDRESS", "HCA", "PIN_NO", "POSTCODE", "DATE_ACCREDITED", "EMAIL", "OFFICE_NO", "FOCAL_POINT", "FP_EMAIL", "FP_MOBILE_NO", "logo_link"];
    protected $primaryKey = "AGENCY_ID";

    protected $appends = ['logo_url'];

    public function focalPoints(){
    	return $this->hasMany('App\Models\AgencyFocalPoint', 'AGENCY_HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
    }

    public function getLogoUrlAttribute(){
    	return Storage::disk('local')->url($this->logo_link);
    }

    public function focalpointMapping(){
        return $this->belongsToMany(\App\Models\AgencyFocalPoint::class, "agency_focalpoint_mappings", "AGENCY_ID", "FOCAL_POINT_ID");
    }

    public function applications(){

    }
}
