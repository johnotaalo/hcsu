<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrincipalDependentPassport extends Model
{
    protected $primaryKey = "ID";
    protected $table = "DEPENDENT_PASSPORTS";

    protected $fillable = ['DEPENDENT_ID', 'PASSPORT_NO', 'PASSPORT_TYPE', 'ISSUE_DATE', 'EXPIRY_DATE', 'PLACE_OF_ISSUE', 'COUNTRY_OF_ISSUE', 'OLD_REF_ID'];
}
