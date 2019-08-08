<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class StaffPassport extends Model
{
	protected $connection = "old_pm";
	protected $table = "unon_sm_passport";
	protected $primaryKey = "record_id";

	public function passportType(){
		return $this->belongsTo('\App\Models\PassportType', 'passport_type', 'PPT_TYPE');
	}
}
