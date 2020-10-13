<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DLDuplicate extends Model
{
    protected $connection = 'pm_data';
    protected $table = "DL_03";
}
