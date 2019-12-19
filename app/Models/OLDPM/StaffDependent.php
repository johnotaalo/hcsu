<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class StaffDependent extends Model
{
    protected $connection = "old_pm";
	protected $table = "unon_sm_dependants";
	protected $primaryKey = "record_id";
}
