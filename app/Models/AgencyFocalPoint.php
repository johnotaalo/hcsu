<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgencyFocalPoint extends Model
{
	protected $connection = "2019";
    protected $table = "ref_agency_focal_points";
    protected $fillable = ["AGENCY_HOST_COUNTRY_ID","INDEX_NO","LAST_NAME","OTHER_NAMES","EXTENSION","MOBILE_NO","EMAIL","STATUS"];
}
