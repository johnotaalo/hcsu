<?php

namespace App\Models\PM;
use Illuminate\Database\Eloquent\Model;

class Application extends Model{
	protected $connection = "pm";
	protected $table = "APPLICATION";
	protected $primaryKey = "APP_NUMBER";

	protected $appends = ["process"];

	public function getProcessAttribute(){
		$process_data = \DB::connection($this->connection)->select("SELECT * FROM CONTENT WHERE CON_CATEGORY = 'PRO_TITLE' AND CON_ID = '{$this->PRO_UID}' LIMIT 1");
		return ($process_data) ? $process_data[0] : [];
	}

	public function creator(){
		return $this->belongsTo(\App\Models\PM\User::class, "APP_INIT_USER", "USR_UID");
	}
}