<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherClient extends Model
{
	public function agency(){
		return $this->belongsTo(\App\Models\Agency::class, 'AFFILIATED_AGENCY', 'AGENCY_ID');
	}
}
