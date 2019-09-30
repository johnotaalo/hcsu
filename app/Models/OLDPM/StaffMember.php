<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class StaffMember extends Model
{
    protected $connection = "old_pm";
    protected $table = "unon_staff_member";
    protected $primaryKey = "record_id";

    protected $appends = ['pin', 'dl', 'principal_passports', 'principal_diplomatic_cards'];

    public function pins(){
      return $this->hasMany('App\Models\OLDPM\StaffPIN', 'index_no', 'index_no');
    }

    public function rno(){
      return $this->hasOne('App\Models\OLDPM\StaffRNO', 'index_no', 'index_no');
    }

    public function drivingLicenses(){
      return $this->hasMany('App\Models\OLDPM\StaffDL', 'index_no', 'index_no');
    }

    public function contracts(){
      return $this->hasMany('App\Models\OLDPM\StaffContract', 'index_no', 'index_no');
    }

    public function passports(){
      return $this->hasMany('App\Models\OLDPM\StaffPassport', 'index_no', 'index_no');
    }

    public function diplomaticCards(){
      return $this->hasMany('\App\Models\OLDPM\StaffDipID', 'index_no', 'index_no');
    }

    public function getPrincipalDiplomaticCardsAttribute(){
      return $this->diplomaticCards()->whereIn('owner_code', ['01', ''])->get();
    }

    public function getPrincipalPassportsAttribute(){
      return $this->passports()->whereIn('owner_code', ['01', ''])->get();
    }

    public function getPinAttribute(){
      return $this->pins()->where('owner_code', '01')->first();
    }

    public function getDlAttribute(){
      return $this->drivingLicenses()->where('owner_code', '01')->first();
    }

    public function principal(){
      return $this->belongsTo(\App\Models\Principal::class, "record_id", "OLD_REF_ID");
    }
}
