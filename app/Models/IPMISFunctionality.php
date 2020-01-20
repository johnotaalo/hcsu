<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IPMISFunctionality extends Model
{
    protected $table = "IPMIS_Functionality";

    protected $fillable = ['PROCESS_UID', 'PROCESS_NAME', 'IPMIS_FUNCTIONAL'];
}
