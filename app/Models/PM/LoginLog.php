<?php

namespace App\Models\PM;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
	protected $connection = "pm";
	protected $table = "LOGIN_LOG";
	protected $primaryKey = "LOG_ID";

	public function user(){
		return $this->belongsTo(\App\Models\PM\User::class, "USR_UID", "USR_UID");
	}
}
