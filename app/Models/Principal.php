<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

class Principal extends Model
{
    protected $primaryKey = "ID";
    protected $table = "PRINCIPAL";

    protected $appends = ["image_link"];

    protected $fillable = ["HOST_COUNTRY_ID", "LAST_NAME", "OTHER_NAMES", "EMAIL", "MOBILE_NO", "OFFICE_NO", "R_NO", "PIN_NO", "DL_NO", "MARITAL_STATUS", "IMAGE", "DATE_OF_BIRTH", "GENDER", "ADDRESS", "RESIDENCE"];

    public function contracts(){
    	return $this->hasMany('\App\Models\PrincipalContract', 'PRINCIPAL_ID', 'HOST_COUNTRY_ID');
    }

    public function dependents(){
    	return $this->hasMany('\App\Models\PrincipalDependent', 'PRINCIPAL_ID', 'HOST_COUNTRY_ID');
    }

    public function passports(){
    	return $this->hasMany('\App\Models\PrincipalPassport', 'PRINCIPAL_ID', 'HOST_COUNTRY_ID');
    }
    public function vehicles(){
        return $this->hasMany('\App\Models\VehicleOwner', 'HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
    }

    public function getImageLinkAttribute($value){
    	return Storage::url($this->IMAGE);
    }
}
