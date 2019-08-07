<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\OLDPM\StaffMember;
use App\Models\Principal;
use App\Models\PrincipalContract;

class DataController extends Controller
{
    function importData(){
      // Kamerika - 12391
      // Legesse - 6334
      $staffMember = StaffMember::find(6334);
      return $staffMember;
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

      // Principal Contract Details
      $contract = new PrincipalContract();

      dd($principal);
    }
}
