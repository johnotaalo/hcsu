<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class AppDelegation extends Model
{
  protected $connection = "old_pm";
  protected $table = "app_delegation";
  public $incrementing = false;
  public $timestamps = false;
  protected $primaryKey = ["APP_UID", "DEL_INDEX"];

  public function app(){
  	return $this->belongsTo(\App\Models\OLDPM\Application::class, "APP_UID", "APP_UID");
  }

  public function user(){
  	return $this->belongsTo(\App\Models\OLDPM\User::class, "USR_UID", "USR_UID");
  }

}
