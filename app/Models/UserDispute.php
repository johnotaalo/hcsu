<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDispute extends Model
{
    protected $fillable = ['user_id','index_no','lastname','othernames','email','agency','merged','merged_host_country_id'];
}
