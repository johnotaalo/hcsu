<?php

namespace App\Http\Controllers\Api;

ini_set("memory_limit", "-1");
ini_set("max_execution_time", "-1");

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\OLDPM\StaffMember;
use App\Models\OLDPM\StaffSpouse;
use App\Models\Principal;
use App\Models\PrincipalContract;
use App\Models\PrincipalDependent;

class DataController extends Controller
{
    function importData(){
      $ids = Principal::where('OLD_REF_ID', '!=', 0)->pluck('OLD_REF_ID')->toArray();
      // dd($ids);
      $members = StaffMember::where('OLD_REF_ID', '!=', 0)->whereNotIn('record_id', $ids)->pluck('record_id');
      // $members = StaffMember::all();
      dd($members);
      foreach ($members as $id) {
            $this->importStaffData($id);
      }
      
    }

    function importSpouseData(){
      // \DB::enableQueryLog();
      $ids = PrincipalDependent::where('RELATIONSHIP_ID', 2)->where('OLD_REF_ID', '!=', NULL)->pluck('OLD_REF_ID')->unique()->toArray();
      $sql = "SELECT record_id FROM unon_sm_spouse WHERE record_id NOT IN (".implode(', ', $ids).")";
      // echo $sql;die;
      $spouses = collect(\DB::connection('old_pm')->select(\DB::raw($sql)))->pluck('record_id');
      // dd($spouses);
      foreach ($spouses as $id) {
            $this->getSpouseData($id);
      }
    }

    function importPrincipalNationalities(){
      $principals = Principal::where('NATIONALITY', NULL)->get();
      foreach ($principals as $principal) {
            $this->updateStaffNationality($principal);
      }
    }

    function getSpouseData($record_id){
      $spouse = StaffSpouse::find($record_id);

      $no_staff = [];
      $no_principal = [];

      if($spouse->staff){
            if($spouse->staff->principal){
                  $principalDependent = new PrincipalDependent();

                  // if (\Carbon\Carbon::parse($spouse->date_of_birth) === false) {
                  //       die($spouse->date_of_birth);
                  // }
                  // if (strtotime($spouse->date_of_birth) === false) {
                  //       die($spouse->date_of_birth);
                  // }

                  $principalDependent->INDEX_NO = NULL;
                  $principalDependent->LAST_NAME = $spouse->last_name;
                  $principalDependent->OTHER_NAMES = $spouse->other_names;
                  $principalDependent->RELATIONSHIP_ID = 2;
                  $principalDependent->COUNTRY = $spouse->nationality;
                  $principalDependent->EMPLOYMENT_DETAILS = $spouse->employment_details;
                  $principalDependent->DATE_OF_BIRTH = ($spouse->date_of_birth != "0000-00-00 00:00:00" && $spouse->date_of_birth != null) ? $spouse->date_of_birth : NULL;
                  $principalDependent->OLD_REF_ID = $record_id;

                  $principalDependent->PRINCIPAL_ID = $spouse->staff->principal->HOST_COUNTRY_ID;
                  $principalDependent->PASSPORT_NO = null;

                  $principalDependent->save();
            }else{
                  $no_principal[] = $record_id;
                  echo "{$record_id} has no principal data <br/>";
            }

            // dd($principalDependent);
      }else{
            $no_staff[] = $record_id;
            // echo "{$record_id} has no staff data <br/>";
      }

      // echo "no staff <br/>";
      // echo "<pre>";print_r($no_staff);

    }

    function updateStaffNationality($principal){
      $staffMember = StaffMember::find($principal->OLD_REF_ID);

      if($staffMember){
            $nationality = \App\Models\Country::where('name', 'LIKE', "%{$staffMember->nationality}%")->orWhere('official_name', 'LIKE', "%{$staffMember->nationality}%")->orWhere('pm_abbrev', 'LIKE', "%{$staffMember->nationality}%")->first();
            $nationality_id = ($nationality) ? $nationality->id : null;
            // echo $staffMember->last_name . " => {$staffMember->nationality} => " . $nationality_id . "<br/>";
            $principal->PLACE_OF_BIRTH = $staffMember->place_of_birth;
            $principal->NATIONALITY = $nationality_id;

            $principal->save();
      }
      
    }

    function importStaffData($record_id){
      $staffMember = StaffMember::find($record_id);
      // dd($staffMember->principal_diplomatic_cards);
      // return $staffMember;
      // Principal Details
      $principal = new Principal();
      $principal->HOST_COUNTRY_ID = strtotime(time());
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
      $principal->PLACE_OF_BIRTH = $staffMember->place_of_birth;

      // dd($principal);

      $principal->save();

      $host_country_id = $this->createHostCountryID($principal->ID);
      $principal->HOST_COUNTRY_ID = $host_country_id;
      $principal->save();

      $pdata = Principal::where('HOST_COUNTRY_ID', $host_country_id)->first();
      $this->updateStaffNationality($pdata);

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

      foreach ($principalContracts as $contract) {
            $insertedContract = \App\Models\PrincipalContract::create($contract);

            \App\Models\PrincipalContractRenewal::create([
                  "START_DATE" => $contract['CONTRACT_FROM'], 
                  "END_DATE" => $contract['CONTRACT_TO'], 
                  "GRADE_ID" => $contract['GRADE_ID'], 
                  "GRADE" => $contract['GRADE'], 
                  "CONTRACT_ID" => $insertedContract->ID
            ]);
      }

      // \App\Models\PrincipalContract::insert($principalContracts);

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

    function createHostCountryID($new_id = null){
      return "10000000" + $new_id;
    }

    function getClient(Request $request){
      $clientType = $request->query('type');
      $number = $request->query('query');

      if ($clientType == "clients") {
      }else if ($clientType == "focalpoint") {
      }
    }

    function importDependentPassports(){
      $passports = \App\Models\OLDPM\StaffPassport::where('owner_code', '<>', '01')
                    ->where('owner_code', '<>', '')
                    ->whereNotNull('owner_code')
                    // ->limit(10)
                    ->with(['staff'])      
                    ->get();

      $cleanedPassportData = $passports->map(function($passport){
        $dep = $newDep = new \StdClass;
        $host_country_id = "";
        if ($passport->owner_code == "02") {
          $dep = \App\Models\OLDPM\StaffSpouse::where('owner_code', $passport->owner_code)->where('index_no', $passport->index_no)->first();
        }else{
          $dep = \App\Models\OLDPM\StaffDependent::where('owner_code', $passport->owner_code)->where('index_no', $passport->index_no)->first();
        }
        if($dep){
          $newDep = \App\Models\PrincipalDependent::where('OLD_REF_ID', $dep->record_id)->first();
          $host_country_id = (isset($newDep->HOST_COUNTRY_ID)) ? $newDep->HOST_COUNTRY_ID : "";
        }

        return (object)[
          'details'         =>  $passport,
          "owner_code"      =>  $passport->owner_code,
          'host_country_id' =>  $host_country_id
        ];
      });

      $availablePassports = $cleanedPassportData->filter(function($data){
        return $data->host_country_id != "";
      })->all();

      $insertData = collect($availablePassports)->map(function($passport){
        $passport_type = \App\Models\PassportType::where('PPT_TYPE', $passport->details->passport_type)->first();
        return [
          'DEPENDENT_ID'      =>  $passport->host_country_id,
          'PASSPORT_NO'       =>  $passport->details->passport_no,
          'PASSPORT_TYPE'     =>  ($passport_type) ? $passport_type->ID : 0,
          'ISSUE_DATE'        =>  $passport->details->issue_date,
          'EXPIRY_DATE'       =>  $passport->details->expiry_date,
          'PLACE_OF_ISSUE'    =>  $passport->details->place_of_issue,
          'COUNTRY_OF_ISSUE'  =>  $passport->details->country_of_issue,
          'OLD_REF_ID'        =>  $passport->details->record_id
        ];
      })->toArray();

      \App\Models\PrincipalDependentPassport::query()->truncate();
      \App\Models\PrincipalDependentPassport::insert($insertData);

      // dd($insertData);

      // dd($cleanedPassportData->toArray());
      // $cleanedPassportDataArray = $cleanedPassportData->toArray();
      // dd($availablePassports);
      // $available = array_diff(
      //   $cleanedPassportDataArray, 
      //   $unknown
      // );

      // dd("Known: " . count($available) . " Unkown: " . count($unknown));

      // $staffMemberIndices = $passports->map(function($passport){
      //   if($passport->staff->index_no != "" || $passport->staff->index_no != null)
      //     return $passport->staff->index_no;
      // })->toArray();

      // $dependents = \App\Models\PrincipalDependent::where

      // dd($staffMemberIndices);

      return $insertData;
    }

    function getDependentOptions(){
      return [
        'relationships'   =>  \App\Models\Relationship::whereNotIn('RELATIONSHIP', ['Principal'])->get(),
        'nationalities'   =>  \App\Models\Country::all()
      ];
    }

    function getDomesticWorkerOptions(){
      return [
        'countries'       => \App\Models\Country::all(),
        'passportTypes'   =>  \App\Models\PassportType::where('ID', 2)->get()
      ];
    }

    function addDesignation(Request $request){
      $designation = $request->designation;
      $grade_id = $request->grade_id;

      $grade = \App\Models\Grade::where('ID', $grade_id)->firstOrFail();
      $designation = \App\Models\ContractDesignation::firstOrCreate(
        ['GRADE' => $grade->GRADE, 'DESIGNATION' => $designation, 'CATEGORY' => $grade->CATEGORY]
      );
      return $designation;
    }

    function pendingPrincipals(Request $request){
      $searchQueries = $request->get('query');
      $limit = $request->get('limit');
      $page = $request->get('page');
      $ascending = $request->get('ascending');
      $byColumn = $request->get('byColumn');
      $orderBy = $request->get('orderBy');

      $ids = Principal::pluck('OLD_REF_ID')->toArray();
      $queryBuilder =StaffMember::select('record_id', 'index_no', 'last_name', 'other_names', 'mission')->whereNotIn('record_id', $ids);

      if($searchQueries){
        $queryBuilder->where('last_name', 'LIKE', "%{$searchQueries}%");
        $queryBuilder->orWhere('other_names', 'LIKE', "%{$searchQueries}%");
        $queryBuilder->orWhere('index_no', 'LIKE', "%{$searchQueries}%");
      }

      $count = $queryBuilder->count();
      $queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));
      $staff_members = $queryBuilder->get();
      $cleanedData = [];

      return [
        'data'  => $staff_members,
        'count' =>  $count
      ];
    }

    function importPendingStaff(Request $request){
      $principal = Principal::where('OLD_REF_ID', $request->record_id)->first();
      if(!$principal){
        $this->importStaffData($request->record_id);
        $principal = Principal::where('OLD_REF_ID', $request->record_id)->first();
        $this->updateStaffNationality($principal);
        return ['status' => true, "message" => "{$principal->LAST_NAME}, {$principal->OTHER_NAMES} has successfully been imported with Host Country ID: {$principal->HOST_COUNTRY_ID}"];
      }else{
        return ['status' => false, 'message' => "{$principal->LAST_NAME}, {$principal->OTHER_NAMES} has already been imported with Host Country ID: {$principal->HOST_COUNTRY_ID}"];
      }
    }
}
