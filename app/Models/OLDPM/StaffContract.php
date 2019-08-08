<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class StaffContract extends Model
{
  protected $connection = "old_pm";
  protected $table = "unon_sm_contract";
  protected $primaryKey = "record_id";

  public function agencydata(){
  	return $this->belongsTo('\App\Models\Agency', 'agency', 'ACRONYM');
  }

  public function contractType(){
  	return $this->belongsTo('\App\Models\ContractType', 'contract_type', 'C_TYPE');
  }

  public function gradeType(){
  	return $this->belongsTo('\App\Models\Grade', 'grade', 'GRADE');
  }
}
