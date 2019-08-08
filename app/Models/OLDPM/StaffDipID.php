<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class StaffDipID extends Model
{
	protected $connection = "old_pm";
	protected $table = "unon_sm_diplomatic_id";
	protected $primaryKey = "record_id";
}
