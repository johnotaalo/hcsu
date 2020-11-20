<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApplications extends Model
{
    protected $connection = 'pm_data';
    protected $table = 'USER_APPLICATIONS';

   protected $fillable = ['PROCESS_UID','HOST_COUNTRY_ID','COMMENT','APP_UID','APP_NUMBER','ASSIGNED_TO','SUPERVISOR_COMMENTS','STATUS','SUBMITTED_BY','AUTHENTICATION_SOURCE','CURRENT_USER'];

	public function process(){
		return $this->belongsTo(\App\Models\PM\Process::class, 'PROCESS_UID', 'PRO_UID');
	}
}
