<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnedPlate extends Model
{
    protected $table = "RETURNED_PLATES";

    protected $fillable = ["RNP_DATE"];

    public function plates(){
    	return $this->hasMany(\App\ReturnedPlateList::class, "RETURNED_PLATE_ID");
    }
}
