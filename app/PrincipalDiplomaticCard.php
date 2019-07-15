<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrincipalDiplomaticCard extends Model
{
    protected $fillable = ["HOST_COUNTRY_ID", "DIP_ID_NO", "ISSUE_DATE", "EXPIRY_DATE"];
}
