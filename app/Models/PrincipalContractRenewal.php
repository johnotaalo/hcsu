<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class PrincipalContractRenewal extends Model
{
	protected $primaryKey = "ID";
    protected $table = "PRINCIPAL_CONTRACT_RENEWALS";

    protected $fillable = ["START_DATE", "END_DATE", "GRADE_ID", "GRADE", "CONTRACT_ID"];

    protected $appends = ['grade'];

    public function getGradeAttribute(){
    	$grade = \App\Models\GRADE::where('ID', $this->GRADE_ID)->first();
        return $grade;
    }
}
