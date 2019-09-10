<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgencyFocalPoint extends Model
{
	protected $connection = "2019";
    protected $table = 'ref_agency_focal_points';

    protected $primaryKey = "ID";

    public function agency(){
    	return $this->belongsTo(\App\Models\Agency::class, 'AGENCY_HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
    }
}
