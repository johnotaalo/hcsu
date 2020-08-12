<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdobeSignDocuments extends Model
{
    protected $table = "ADOBE_SIGN_DOCUMENTS";

    protected $fillable = ["DOCUMENT_ID", "AGREEMENT_ID", "AGREEMENT_STATUS", "PAYLOAD", "URLS"];
}
