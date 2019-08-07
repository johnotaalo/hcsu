<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class StaffContract extends Model
{
  protected $connection = "old_pm";
  protected $table = "unon_sm_contract";
  protected $primaryKey = "record_id";
}
