<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleOwner extends Model
{
    protected $table = "VEHICLE_OWNER";
	protected $primaryKey = "ID";
	protected $fillable = ["HOST_COUNTRY_ID", "REG_PLATE_ID", "STATUS", "VEHICLE_ID", "LOG_BOOK_NO"];
	protected $connection = "mysql";

	// public function client(){
	// 	$clientType = identify_hcsu_client($this->HOST_COUNTRY_ID);
	// 	if ($clientType == "staff") {
	// 		return $this->belongsTo(\App\Models\Principal::class, 'HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
	// 	}elseif ($clientType == "dependent") {
	// 		return $this->belongsTo(\App\Models\PrincipalDependent::class, 'HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
	// 	}else {
	// 		return $this->belongsTo(\App\Models\Agency::class, 'HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
	// 	}

	// 	return ['clientType' => $clientType, $this->HOST_COUNTRY_ID];
	// }

	public function principal(){
		return $this->belongsTo(\App\Models\Principal::class, 'HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
	}

	public function dependent(){
		return $this->belongsTo(\App\Models\PrincipalDependent::class, 'HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
	}

	public function agency(){
		return $this->belongsTo(\App\Models\Agency::class, 'HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
	}
}
