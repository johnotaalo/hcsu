<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
  protected $connection = "old_pm";
  protected $table = "application";
  protected $primaryKey = "APP_NUMBER";
  public $timestamps = false;

  public function delegations(){
  	return $this->hasMany(\App\Models\OLDPM\AppDelegation::class, "APP_UID", "APP_UID");
  }

  public function process(){
  	return $this->belongsTo(\App\Models\OLDPM\Process::class, "PRO_UID", "PRO_UID");
  }

  public function currentUser(){
  	return $this->hasOne(\App\Models\OLDPM\PMUser::class, "USR_UID", "APP_CUR_USER");
  }
}
