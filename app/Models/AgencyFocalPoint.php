<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class AgencyFocalPoint extends Authenticatable
{
	use Notifiable;

	protected $connection = "2019";
    protected $table = "ref_agency_focal_points";

    protected $primaryKey = "ID";

    protected $fillable = ["AGENCY_HOST_COUNTRY_ID","INDEX_NO","LAST_NAME","OTHER_NAMES","EXTENSION","MOBILE_NO","EMAIL","STATUS", "USERNAME", "PASSWORD"];
    protected $hidden = ["PASSWORD", "remember_token"];

    function agency(){
    	return $this->belongsTo('\App\Models\Agency', 'AGENCY_HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
    }

    function getEmailAttribute(){
    	return $this->attributes['EMAIL'];
    }

    function getFullNameAttribute(){
    	return strtoupper($this->attributes['LAST_NAME']) . ", " . ucwords(strtolower($this->attributes['OTHER_NAMES']));
    }

    function agencies(){
        return $this->belongsToMany(\App\Models\Agency::class, "agency_focalpoint_mappings", "FOCAL_POINT_ID", "AGENCY_ID");
    }

    function mapping(){
        return $this->hasMany(\App\Models\AgencyFocalpointMapping::class, "FOCAL_POINT_ID", "ID");
    }

}
