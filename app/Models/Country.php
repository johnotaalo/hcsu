<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
	protected $connection = "2019";
	protected $table = "country";

	protected $fillable = ['pm_abbrev'];

	public $timestamps = false;
}
