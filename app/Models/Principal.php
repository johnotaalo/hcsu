<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

class Principal extends Model
{
    protected $primaryKey = "ID";
    protected $table = "PRINCIPAL";
    protected $connection = "mysql";

    protected $appends = ["image_link", "active_diplomatic_card", "latest_diplomatic_card", "current_arrival", "latest_contract", "fullname", "latest_passport", "spouse", "principal_name"];

    protected $fillable = ["HOST_COUNTRY_ID", "LAST_NAME", "OTHER_NAMES", "EMAIL", "MOBILE_NO", "OFFICE_NO", "R_NO", "PIN_NO", "DL_NO", "MARITAL_STATUS", "IMAGE", "DATE_OF_BIRTH", "PLACE_OF_BIRTH", "NATIONALITY", "GENDER", "ADDRESS", "RESIDENCE", "OLD_REF_ID"];

    public function contracts(){
    	return $this->hasMany('\App\Models\PrincipalContract', 'PRINCIPAL_ID', 'HOST_COUNTRY_ID');
    }

    public function dependents(){
    	return $this->hasMany('\App\Models\PrincipalDependent', 'PRINCIPAL_ID', 'HOST_COUNTRY_ID');
    }

    public function domesticWorkers(){
        return $this->hasMany('\App\Models\PrincipalDomesticWorker', 'PRINCIPAL_ID', 'HOST_COUNTRY_ID');
    }

    public function getSpouseAttribute(){
        return $this->dependents->where('RELATIONSHIP_ID', 2)->first();
    }

    public function passports(){
    	return $this->hasMany('\App\Models\PrincipalPassport', 'PRINCIPAL_ID', 'ID');
    }

    public function getLatestPassportAttribute(){
        $passports = \App\Models\PrincipalPassport::where('PRINCIPAL_ID', $this->ID)->orderBy('EXPIRY_DATE', 'DESC')->first();

        return $passports;
    }
    public function vehicles(){
        return $this->hasMany('\App\Models\VehicleOwner', 'HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
    }

    public function nationality(){
      return $this->hasOne(\App\Models\Country::class, 'id', 'NATIONALITY');
    }

    public function arrivalDepartures(){
        return $this->hasMany('\App\PrincipalArrivalDeparture', 'HOST_COUNTRY_ID', 'HOST_COUNTRY_ID');
    }

    public function getFullnameAttribute(){
      return strtoupper($this->LAST_NAME) . ", " . ucwords(strtolower($this->OTHER_NAMES));
    }

    public function getPrincipalNameAttribute(){
        return format_other_names($this->OTHER_NAMES) . " " . strtoupper($this->LAST_NAME);
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

    public function getLatestContractAttribute(){
        \DB::enableQueryLog();
      // $contract = \App\Models\PrincipalContract::where('PRINCIPAL_ID', $this->HOST_COUNTRY_ID)
      //             ->whereHas('renewals', function($query){
      //               $query->orderBy('END_DATE', 'DESC');
      //             })
      //             ->with('agency')
      //             ->first();
        $sql = "SELECT
                    * 
                FROM
                    PRINCIPAL_CONTRACT PC
                    JOIN PRINCIPAL_CONTRACT_RENEWALS PCR ON PCR.CONTRACT_ID = PC.ID
                    JOIN pm_ref_data.agencies a ON a.AGENCY_ID = PC.AGENCY_ID
                    WHERE PC.PRINCIPAL_ID = {$this->HOST_COUNTRY_ID}
                    ORDER BY PCR.END_DATE DESC
                    LIMIT 1;";

        $data = \DB::select(\DB::raw($sql));
        $contract = [];
        if($data)
            $contract = $data[0];
                  // dd($contract);
                  // \DB::enableQueryLog();
                  // $query = \DB::getQueryLog();
                  // dd($query);
        // $maxContract = $contract->max('CONTRACT_TO');
        // dd($maxContract);
      return $contract;
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
        // return '/photos/principal/' . $this->IMAGE;
    	return Storage::url($this->IMAGE);
    }
}
