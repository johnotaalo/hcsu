<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class StaffPIN extends Model
{
  protected $connection = "old_pm";
  protected $table = "unon_sm_pin_no";
  protected $primaryKey = "record_id";

  public function staffMember(){
    return $this->belongsTo('App\Models\OLDPM\StaffMember', 'index_no', 'index_no');
  }
}
