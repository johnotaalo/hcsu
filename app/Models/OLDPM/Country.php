<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $connection = "old_pm";
    protected $table = "unon_countries_lookup";
}
