<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class StaffDL extends Model
{
  protected $connection = "old_pm";
  protected $table = "unon_sm_driving_licence_no";
  protected $primaryKey = "record_id";
}
