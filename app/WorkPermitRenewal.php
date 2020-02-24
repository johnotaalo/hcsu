<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkPermitRenewal extends Model
{
    protected $table = "IM_01_RENEWALS";
    protected $connection = "pm_data";
}
