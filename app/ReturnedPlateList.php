<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnedPlateList extends Model
{
    protected $table = "RETURNED_PLATES_LIST";

    protected $fillable = ["RETURNED_PLATE_ID", "HOST_COUNTRY_ID", "PLATE_NO"];

    protected $appends = ['client_details', 'client_type'];

    public function rnp(){
    	return $this->belongsTo(\App\ReturnedPlate::class, "RETURNED_PLATE_ID", "id");
    }

    public function client(){
    	$type = identify_hcsu_client($this->HOST_COUNTRY_ID);

    	if ($type == "staff") {
    		return $this->belongsTo(\App\Models\Principal::class, "HOST_COUNTRY_ID", "HOST_COUNTRY_ID");
    	}else if($type == "dependent"){
    		return $this->belongsTo(\App\Models\PrincipalDependent::class, "HOST_COUNTRY_ID", "HOST_COUNTRY_ID");
    	}else{
    		// return $this->belongsTo(\App\Models\Agency::class, "HOST_COUNTRY_ID", "HOST_COUNTRY_ID");
    		return $this->belongsTo(\App\Models\Principal::class, "HOST_COUNTRY_ID", "HOST_COUNTRY_ID");
    	}
    // 	else{
    // 		return $this->belongsTo(\App\Models\Agency::class, "HOST_COUNTRY_ID", "HOST_COUNTRY_ID");
    // 	}
    }

    public function getClientTypeAttribute(){
    	return identify_hcsu_client($this->HOST_COUNTRY_ID);
    }

    public function getClientDetailsAttribute(){
    	$type = identify_hcsu_client($this->HOST_COUNTRY_ID);

    	if ($type == "staff") {
    		return \App\Models\Principal::where('HOST_COUNTRY_ID', $this->HOST_COUNTRY_ID)->first();
    	}else if($type == "dependent"){
    		return \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $this->HOST_COUNTRY_ID)->first();
    	}else{
    		return \App\Models\Agency::where('HOST_COUNTRY_ID', $this->HOST_COUNTRY_ID)->first();
    	}
    }
}
