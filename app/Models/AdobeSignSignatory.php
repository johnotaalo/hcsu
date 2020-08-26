<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdobeSignSignatory extends Model
{
    protected $table = "ADOBE_SIGN_SIGNATORIES";

    protected $fillable = ["last_name", "other_names", "email", "status"];
}
