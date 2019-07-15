<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrincipalDiplomaticCard extends Model
{
	protected $table="PRINCIPAL_DIPLOMATIC_CARDS";
    protected $fillable = ["HOST_COUNTRY_ID", "DIP_ID_NO", "ISSUE_DATE", "EXPIRY_DATE"];
}
