<?php

namespace App\Models\PM;

use Illuminate\Database\Eloquent\Model;

class SubApplication extends Model
{
	protected $connection = "pm";
	protected $table = "SUB_APPLICATION";
	// protected $primaryKey = "APP_UID";

	protected $appends = ['application_details'];

	public function application(){
		return $this->belongsTo(\App\Models\PM\Application::class, "APP_UID", "APP_UID");
	}

	public function parent(){
		return $this->belongsTo(\App\Models\PM\Application::class, "APP_PARENT", "APP_UID");
	}

	public function getApplicationDetailsAttribute(){
		return \App\Models\PM\Application::where('APP_UID', $this->APP_UID)->first();
	}
}
