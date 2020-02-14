<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkPermitEndorsement extends Model
{
	protected $connection = "pm_data";
    protected $table = "IM_01_ENDORSEMENT";
    protected $fillable = ['CASE_NO', 'HOST_COUNTRY_ID', 'DEPENDENTS'];
}
