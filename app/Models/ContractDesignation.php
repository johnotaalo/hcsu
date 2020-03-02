<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractDesignation extends Model
{
	protected $connection = "2019";
    protected $table = "ref_grade_designation";
    protected $fillable = ["GRADE", "DESIGNATION", "CATEGORY"];
    public $timestamps = false;
    protected $primaryKey = "ID";
}
