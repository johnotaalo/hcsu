<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

class Principal extends Model
{
    protected $primaryKey = "ID";
    protected $table = "PRINCIPAL";

    protected $appends = ["image_link", "active_diplomatic_card", "latest_diplomatic_card", "current_arrival"];

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

    public function arrivalDepartures(){
        return $this->hasMany('\App\PrincipalArrivalDeparture', 'HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
    }

    public function getCurrentArrivalAttribute(){
        $arrivals = $this->arrivalDepartures;

        $currentArrival = $arrivals->map(function($arrival){
            if (is_null($arrival->DEPARTURE)) {
                return $arrival;
            }
        })->reject(function ($arrival) {
            return is_null($arrival);
        });

        if (count($currentArrival) > 0) {
            return $currentArrival[0];
        }

        return null;
        
    }

    public function getLatestDiplomaticCardAttribute(){
        $card = \App\PrincipalDiplomaticCard::where('HOST_COUNTRY_ID', $this->HOST_COUNTRY_ID)->orderBy('EXPIRY_DATE', 'DESC')->first();

        return $card;
    }

    public function getActiveDiplomaticCardAttribute(){
        $cards = $this->diplomaticCards;

        $activeCard = $cards->map(function($card){
            if ($card->ISSUE_DATE > \Carbon\Carbon::now() && $card->EXPIRY_DATE < \Carbon\Carbon::now()) {
                return $card;
            }
        })
        ->reject(function($card){
            return is_null($card);
        });

        return $activeCard;
    }

    public function diplomaticCards(){
        return $this->hasMany('\App\PrincipalDiplomaticCard', 'HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
    }

    public function getImageLinkAttribute($value){
    	return Storage::url($this->IMAGE);
    }
}
