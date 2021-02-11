<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApplications extends Model
{
	protected $connection = 'pm_data';
	protected $table = 'USER_APPLICATIONS';

	protected $fillable = ['PROCESS_UID','HOST_COUNTRY_ID','COMMENT','APP_UID','APP_NUMBER','ASSIGNED_TO','SUPERVISOR_COMMENTS','STATUS','SUBMITTED_BY','AUTHENTICATION_SOURCE','CURRENT_USER'];

	protected $appends = ['process', 'applicant_details', 'applicant_type'];

	// public function process(){
	// 	return $this->belongsTo(\App\Models\PM\Process::class, 'PROCESS_UID', 'PRO_UID');
	// }
	public function caseDetails(){
		return $this->hasOne(\App\Models\PM\Application::class, 
			"APP_NUMBER", 
			"APP_NUMBER"
		);
	}

	public function assigned(){
		return $this->hasOne(\App\Models\PM\User::class, "USR_UID", "ASSIGNED_TO");
	}

	public function files(){
		return $this->hasMany(\App\Model\UserApplicationFile::class, 'USER_APPLICATION_ID');
	}

	public function getProcessAttribute(){
		return \App\Models\PM\Process::where('PRO_UID', $this->PROCESS_UID)->first();
	}

	public function getApplicantTypeAttribute(){
		return identify_hcsu_client($this->HOST_COUNTRY_ID);
	}

	public function getApplicantDetailsAttribute(){
		$clientType = identify_hcsu_client($this->HOST_COUNTRY_ID);

		if ($clientType  == "staff") {
			return \App\Models\Principal::where('HOST_COUNTRY_ID', $this->HOST_COUNTRY_ID)->first();
		}else if ($clientType == "agency") {
			return \App\Models\Agency::where('HOST_COUNTRY_ID', $this->HOST_COUNTRY_ID)->first();
		}else{
			return \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $this->HOST_COUNTRY_ID)->first();
		}
	}
}
