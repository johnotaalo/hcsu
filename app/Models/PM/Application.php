<?php

namespace App\Models\PM;
use Illuminate\Database\Eloquent\Model;

class Application extends Model{
	protected $connection = "pm";
	protected $table = "APPLICATION";
	protected $primaryKey = "APP_NUMBER";
}