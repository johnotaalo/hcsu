<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class PrincipalContract extends Model
{
    protected $primaryKey = "ID";
    protected $table = "PRINCIPAL_CONTRACT";
    protected $appends = ['contract_agency', "type", "renewals"];

    protected $fillable = ["PRINCIPAL_ID", "INDEX_NO", "AGENCY_ID", "DESIGNATION", "FUNC_TITLE", "CONTRACT_TYPE_ID"];


    public function principal(){
    	return $this->belongsTo('\App\Models\Principal', 'HOST_COUNTRY_ID', 'PRINCIPAL_ID');
    }

    public function agency(){
    	return $this->belongsTo('\App\Models\Agency', 'AGENCY_ID', 'AGENCY_ID');
    }

    public function getContractAgencyAttribute(){
        $agency = \App\Models\Agency::where('AGENCY_ID', $this->AGENCY_ID)->first();
        return $agency;
    }

    public function renewals(){
    	return $this->hasMany('\App\Models\PrincipalContractRenewal', 'CONTRACT_ID', 'ID');
    }

    public function contract_type(){
        return $this->belongsTo('\App\Models\ContractType', 'CONTRACT_TYPE_ID', 'ID');
    }

    public function getTypeAttribute(){
        $type = \App\Models\ContractType::where('ID', $this->CONTRACT_TYPE_ID)->first();
        return $type;
    }

    public function getRenewalsAttribute(){
        $renewals = \App\Models\PrincipalContractRenewal::where('CONTRACT_ID', $this->ID)->first();
        return $renewals;
    }
}
