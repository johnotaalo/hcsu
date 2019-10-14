<?php

namespace App\Models\PM;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = "pm";
	protected $table = "USERS";
	protected $primaryKey = "USR_ID";
	protected $hidden = ["USR_PASSWORD", "USR_ROLE"];
}
