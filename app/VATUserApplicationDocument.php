<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VATUserApplicationDocument extends Model
{
    protected $connection = "pm_data";
    protected $table = "VAT_01_USER_APPLICATION_DOCUMENTS";
    protected $fillable = ["APPLICATION_ID", "PATH", "DOCUMENT_UID"];
}
