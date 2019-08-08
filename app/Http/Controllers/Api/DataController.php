<?php

namespace App\Http\Controllers\Api;

ini_set("memory_limit", "-1");
ini_set("max_execution_time", "-1");

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\OLDPM\StaffMember;
use App\Models\Principal;
use App\Models\PrincipalContract;

class DataController extends Controller
{
    function importData(){
      $ids = Principal::pluck('OLD_REF_ID')->toArray();
      $members = StaffMember::whereNotIn('record_id', $ids)->pluck('record_id');
      foreach ($members as $id) {
            $this->importStaffData($id);
      }
      
    }

    function importStaffData($record_id){
      $staffMember = StaffMember::find($record_id);
      // dd($staffMember->principal_diplomatic_cards);
      // return $staffMember;
      // Principal Details
      $principal = new Principal();
      $principal->LAST_NAME = $staffMember->last_name;
      $principal->OTHER_NAMES = $staffMember->other_names;
      $principal->EMAIL = $staffMember->email_address;
      $principal->MOBILE_NO = $staffMember->phone_no;
      $principal->OFFICE_NO = $staffMember->office_phone_no;
      $principal->R_NO = ($staffMember->rno) ? $staffMember->rno->residence_no : null;
      $principal->PIN_NO = ($staffMember->pin) ? $staffMember->pin->pin_no : NULL;
      $principal->DL_NO = ($staffMember->dl) ? $staffMember->dl->driving_licence_no : NULL;
      $principal->MARITAL_STATUS = $staffMember->marital_status;
      $principal->IMAGE = NULL;
      $principal->DATE_OF_BIRTH = $staffMember->date_of_birth;
      $principal->GENDER = $staffMember->gender;
      $principal->ADDRESS = $staffMember->address;
      $principal->RESIDENCE = $staffMember->residence;
      $principal->OLD_REF_ID = $staffMember->record_id;

      // dd($principal);

      $principal->save();

      $host_country_id = $this->createHostCountryID($principal->ID);
      $principal->HOST_COUNTRY_ID = $host_country_id;
      $principal->save();

      // Principal Contract Details
      $principalContracts = $staffMember->contracts->map(function($contract) use ($host_country_id){
            // dd($contract->gradeType);
            return [
                  "PRINCIPAL_ID" => $host_country_id, 
                  "INDEX_NO" => $contract->index_no,
                  "AGENCY_ID" => ($contract->agencydata != null) ? $contract->agencydata->AGENCY_ID : null, 
                  "DESIGNATION"=> $contract->designation,
                  "FUNC_TITLE" => $contract->functional_title, 
                  "CONTRACT_TYPE_ID" => ($contract->contractType != null) ? $contract->contractType->ID : null,
                  "GRADE_ID" => ($contract->gradeType != null) ? $contract->gradeType->ID : null, 
                  "GRADE" => $contract->grade, 
                  "CONTRACT_FROM" => $contract->contract_start, 
                  "CONTRACT_TO" => $contract->contract_end
            ];
      })->toArray();

      \App\Models\PrincipalContract::insert($principalContracts);

      $principalPassports = collect($staffMember->principal_passports)->map(function($passport) use ($principal) {
            return [
                  "PRINCIPAL_ID" => $principal->ID, 
                  "PASSPORT_TYPE_ID" => ($passport->passportType != null) ? $passport->passportType->ID : null, 
                  "PASSPORT_NO" => $passport->passport_no, 
                  "PLACE_OF_ISSUE" => $passport->place_of_issue, 
                  "COUNTRY_OF_ISSUE" => $passport->country_of_issue, 
                  "ISSUE_DATE" => ($passport->issue_date == "0000-00-00 00:00:00") ? NULL : $passport->issue_date, 
                  "EXPIRY_DATE" => ($passport->expiry_date == "0000-00-00 00:00:00") ? NULL : $passport->expiry_date
            ];
      })->toArray();

      \App\Models\PrincipalPassport::insert($principalPassports);

      $principalDiplomaticCards = collect($staffMember->principal_diplomatic_cards)->map(function($card) use ($host_country_id){
            return [
                  "HOST_COUNTRY_ID" => $host_country_id, 
                  "DIP_ID_NO" => $card->diplomatic_id_no, 
                  "ISSUE_DATE" => ($card->issue_date == "0000-00-00 00:00:00") ? "2001-01-01" : $card->issue_date, 
                  "EXPIRY_DATE" => ($card->expiry_date == "0000-00-00 00:00:00") ? "2001-01-01" : $card->expiry_date
            ];
      })->toArray();

      \App\PrincipalDiplomaticCard::insert($principalDiplomaticCards);

      $principalArrivalDeparture = new \App\PrincipalArrivalDeparture();

      $principalArrivalDeparture->HOST_COUNTRY_ID = $host_country_id;
      $principalArrivalDeparture->ARRIVAL = ($staffMember->date_of_arrival == "0000-00-00 00:00:00") ? null : $staffMember->date_of_arrival;
      $principalArrivalDeparture->DEPARTURE = ($staffMember->date_of_departure == "0000-00-00 00:00:00") ? null : $staffMember->date_of_departure;

      $principalArrivalDeparture->save();
    }

    function createHostCountryID($new_id){
      return "10000000" + $new_id;
    }
}
