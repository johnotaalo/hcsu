<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = "ref_currencies";
    protected $connection = "2019";

    protected $fillable = [
    	'CODE',
    	'NAME',
    	'SYMBOL'
    ];
}
