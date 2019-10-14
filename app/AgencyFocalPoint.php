<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class AgencyFocalPoint extends Model
{
	use \Illuminate\Notifications\Notifiable;

	protected $connection = "2019";
    protected $table = 'ref_agency_focal_points';

    protected $primaryKey = "ID";

    public function agency(){
    	return $this->belongsTo(\App\Models\Agency::class, 'AGENCY_HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
    }

    public function routeNotificationForMail($notification)
    {
        return $this->EMAIL;
    }
}
