<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherClient extends Model
{
	public function agency(){
		return $this->belongsTo(\App\Models\Agency::class, 'AFFILIATED_AGENCY', 'AGENCY_ID');
	}

	public function nationality(){
		return $this->belongsTo(\App\Models\Country::class, 'NATIONALITY');
	}

	public function passportCountry(){
		return $this->belongsTo(\App\Models\Country::class, 'COUNTRY_OF_ISSUE');
	}
}
