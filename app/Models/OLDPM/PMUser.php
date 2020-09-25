<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class PMUser extends Model
{
  protected $connection = "old_pm";
  protected $table = "users";
}
