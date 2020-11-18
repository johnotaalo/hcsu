<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApplications extends Model
{
    protected $connection = 'pm_data';
    protected $table = 'USER_APPLICATIONS';
}
