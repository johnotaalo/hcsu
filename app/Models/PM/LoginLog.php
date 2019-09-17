<?php

namespace App\Models\PM;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
	protected $connection = "pm";
	protected $table = "LOGIN_LOG";
	protected $primaryKey = "LOG_ID";
}
