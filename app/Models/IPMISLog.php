<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IPMISLog extends Model
{
    protected $connection = "pm_data";
    protected $table = "CASE_IPMIS_LOG";
}
