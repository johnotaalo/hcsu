<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use App\Models\Principal;
use App\Models\PrincipalDependent;
use App\Models\PrincipalPassport;
use App\Models\PrincipalContract;
use App\Models\PrincipalContractRenewal;

use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

use Illuminate\Support\Facades\Storage;

class PrincipalController extends Controller
{
    function getPrincipalOld(Request $request){
        $length = $request->input('length');
        $orderBy = $request->input('column'); //Index
        $orderByDir = $request->input('dir', 'asc');
        $searchValue = $request->input('search');

        $data = Principal::select('IMAGE', 'HOST_COUNTRY_ID', 'LAST_NAME', 'OTHER_NAMES', 'EMAIL', 'MOBILE_NO', 'OFFICE_NO')
                                    ->where('LAST_NAME', 'LIKE', "%{$searchValue}%")
                                    ->orWhere('OTHER_NAMES', 'LIKE', "%{$searchValue}%")
                                    ->orWhere('HOST_COUNTRY_ID', 'LIKE', "%{$searchValue}%")
                                    ->orderBy($orderBy, $orderByDir)
                                    ->paginate($length);

        return new DataTableCollectionResource($data);;
    }

    function getPrincipal(Request $request){
    	$searchQueries = $request->get('query');
        $limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $fields = [
        	"LAST_NAME" 	=>	"last_name",
        	"OTHER_NAMES"	=>	"other_names",
        	"EMAIL"			=>	"email_address"
        ];

        $queryBuilder = Principal::select('IMAGE', 'HOST_COUNTRY_ID', 'LAST_NAME', 'OTHER_NAMES', 'EMAIL', 'MOBILE_NO', 'OFFICE_NO');

        if($searchQueries){
            $queryBuilder->where('LAST_NAME', 'LIKE', "%{$searchQueries}%");
            $queryBuilder->orWhere('OTHER_NAMES', 'LIKE', "%{$searchQueries}%");
            $queryBuilder->orWhere('HOST_COUNTRY_ID', 'LIKE', "%{$searchQueries}%");
        }

        $count = $queryBuilder->count();

        $queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));
    	$principals = $queryBuilder->get();

        // dd($principals);

    	$cleanedData = [];
    	$today = \Carbon\Carbon::now();

    	foreach ($principals as $key => $principal) {
            $cleanedData[$key]['image'] = ($principal->IMAGE) ? route('principal-photo', ['host_country_id' => $principal->HOST_COUNTRY_ID]) : "/storage/";
    		$cleanedData[$key]['last_name'] = $principal->LAST_NAME;
    		$cleanedData[$key]['other_names'] = $principal->OTHER_NAMES;
    		$cleanedData[$key]['email_address'] = $principal->EMAIL;
            $cleanedData[$key]['id'] = $principal->ID;
            $cleanedData[$key]['host_country_id'] = $principal->HOST_COUNTRY_ID;
			// $agency = "N/A";
   //  		foreach($principal->contracts as $contract){
   //  			if($contract->renewals){
   //  				foreach ($contract->renewals as $renewal) {
   //  					$start_date = \Carbon\Carbon::parse($renewal->START_DATE);
   //  					$end_date = \Carbon\Carbon::parse($renewal->END_DATE);
   //  					if($today->between($start_date, $end_date)){
   //  						if($contract->agency){
			// 					$agency = $contract->agency->ACRONYM;
			// 				}
   //  					}
   //  				}
			// 	}
   //  		}
    		// $cleanedData[$key]['agency'] = $agency;
    	}
    	return [
    		'data' 	=> $cleanedData,
    		'count'	=>	$count
    	];
    }

    function searchPrincipal(Request $request){
        $query = $request->q;
        return Principal::
                where('LAST_NAME', 'LIKE', "%{$query}%")
                ->orWhere('OTHER_NAMES', 'LIKE', "%{$query}%")
                ->orWhere('PIN', 'LIKE', "%{$query}%")
                ->orWhere('HOST_COUNTRY_ID', 'LIKE', "%{$query}%")
                ->limit(20)
                ->get();
    }

    function searchDependent(Request $request){
      $query = $request->q;

      return PrincipalDependent::
              where('LAST_NAME', 'LIKE', "%{$query}%")
              ->orWhere('OTHER_NAMES', 'LIKE', "%{$query}%")
              ->orWhere('HOST_COUNTRY_ID', 'LIKE', "%{$query}%")
              ->orWhereHas('principal', function ($modelQuery) use ($query) {
                $modelQuery->where('LAST_NAME', 'LIKE', "%{$query}%")
                ->orWhere('OTHER_NAMES', 'LIKE', "%{$query}%");
              })
              ->limit(20)
              ->with('relationshipX', 'principal')
              ->get();
    }

    function getDependent(Request $request){
      $host_country_id = $request->host_country_id;

      return PrincipalDependent::where('HOST_COUNTRY_ID', $host_country_id)->with('principal', 'relationshipX')->first();
    }

    function updateDependent(Request $request){
        $host_country_id = $request->host_country_id;

        $dependent = PrincipalDependent::where('HOST_COUNTRY_ID', $host_country_id)->first();

        foreach ($request->dependent as $key => $value) {
            $dependent->$key = $value;
        }

        return ['status' => $dependent->update()];
    }

    function addDependentPassport(Request $request){
        $host_country_id = $request->host_country_id;
        $inputData = array_merge($request->all(), ['DEPENDENT_ID' => $host_country_id]);

        $inputData['ISSUE_DATE'] = date('Y-m-d', strtotime($inputData['ISSUE_DATE']));
        $inputData['EXPIRY_DATE'] = date('Y-m-d', strtotime($inputData['EXPIRY_DATE']));

        $data = \App\Models\PrincipalDependentPassport::firstOrNew($inputData);
        $res = $data->save();

        if($res == 1){
            return $inputData;
        }
    }

    function editDependentPassport(Request $request){
        $passport_id = $request->passport_id;
        $inputData = $request->all();

        $inputData['ISSUE_DATE'] = date('Y-m-d', strtotime($inputData['ISSUE_DATE']));
        $inputData['EXPIRY_DATE'] = date('Y-m-d', strtotime($inputData['EXPIRY_DATE']));

        $res = \App\Models\PrincipalDependentPassport::find($passport_id)->update($inputData);

        return ['status' => $res];
    }

    function deleteDependentPassport(Request $request){
        $passport_id = $request->passport_id;

        return ['status' => \App\Models\PrincipalDependentPassport::destroy($passport_id)];
    }

    function add(Request $request){
        $case_no = ($request->query('case_no')) ? $request->query('case_no') : "";
        // echo $case_no;die;
        $principal = new Principal();
        $imagePath = null;
        if(null !== $request->file('principalPhotoFile')){
            $imagePath = $request->file('principalPhotoFile')->store('principal_photos');
        }

        $principal->LAST_NAME = strtoupper($request->input('lastName'));
        $principal->OTHER_NAMES = ucwords(strtolower($request->input('otherNames')));
        $principal->EMAIL = $request->input('email');
        $principal->MOBILE_NO = $request->input('mobileNo');
        $principal->OFFICE_NO = $request->input('officeNo');
        $principal->MARITAL_STATUS = $request->input('maritalStatus');
        $principal->DATE_OF_BIRTH = \Carbon\Carbon::parse($request->input('dob'));
        $principal->GENDER = $request->input('gender');
        $principal->ADDRESS = $request->input('Address');
        $principal->RESIDENCE = $request->input('residence');
        $principal->IMAGE = $imagePath;
        $principal->PIN_NO = strtoupper($request->pin);
        $principal->R_NO = $request->residenceNo;
        $principal->DL_NO = strtoupper($request->drivingLicense);
        $principal->PLACE_OF_BIRTH = $request->place_of_birth;
        $principal->NATIONALITY = (\App\Models\Country::where('iso_3', $request->nationality['id'])->first())->id;

        $principal->save();

        $principalId = $principal->ID;

        $host_country_id = 10000000 + $principalId;

        $principal->HOST_COUNTRY_ID = $host_country_id;

        $principal->save();
        if (isset($_POST['dependents'])) {
            foreach ($request->input('dependents') as $index => $dependent) {
                $principalDependent = new PrincipalDependent();

                $dependentFilePath = null;

                if(isset($request->file('dependents')[$index])){
                    $dependentFilePath = $request->file('dependents')[$index]['imageFile']->store('dependent_photos');
                }

                $principalDependent->PRINCIPAL_ID = $host_country_id;
                $principalDependent->LAST_NAME = $dependent['lastName'];
                $principalDependent->OTHER_NAMES = $dependent['otherNames'];
                $principalDependent->RELATIONSHIP_ID = $dependent['relationshipType']['id'];
                $principalDependent->COUNTRY = $dependent['country']['label'];
                $principalDependent->EMPLOYMENT_DETAILS = $dependent['employment'];
                $principalDependent->PASSPORT_NO = $dependent['passport'];
                $principalDependent->DATE_OF_BIRTH = \Carbon\Carbon::parse($dependent['dob']);
                $principalDependent->IMAGE = $dependentFilePath;

                $principalDependent->save();

                $host_country_id = 20000000 + $principalDependent->id;

                $principalDependent->HOST_COUNTRY_ID = $host_country_id;

                $principalDependent->save();

            }
        }

        if(isset($_POST['passports'])){
            foreach ($request->input('passports') as $key => $passport) {
                $principalPassport = new PrincipalPassport();

                $principalPassport->PRINCIPAL_ID = $principal->HOST_COUNTRY_ID;
                $principalPassport->PASSPORT_TYPE_ID = $passport["passportType"];
                $principalPassport->PASSPORT_NO = $passport["passportNo"];
                $principalPassport->PLACE_OF_ISSUE = $passport["place_issue"];
                $principalPassport->COUNTRY_OF_ISSUE = $passport['country_issue']['label'];
                $principalPassport->ISSUE_DATE = \Carbon\Carbon::parse($passport['date_issue']);
                $principalPassport->EXPIRY_DATE = \Carbon\Carbon::parse($passport['expiry_date']);

                $principalPassport->save();
            }
        }

        if ($_POST['contract']) {
            $principalContract = new PrincipalContract();
            $contractRenewal = new PrincipalContractRenewal();

            $principalContract->PRINCIPAL_ID = $principal->HOST_COUNTRY_ID;
            $principalContract->INDEX_NO = $request->input('contract')['indexNo'];
            $principalContract->AGENCY_ID = $request->input('contract')['agency']['id'];
            $principalContract->DESIGNATION = $request->input('contract')['designation']["id"];
            $principalContract->FUNC_TITLE = $request->input('contract')['functionalTitle'];
            $principalContract->CONTRACT_TYPE_ID = $request->input('contract')['contractType']['id'];

            $principalContract->save();

            $contractId = $principalContract->ID;

            $contractRenewal->CONTRACT_ID = $contractId;
            $contractRenewal->START_DATE = \Carbon\Carbon::parse($request->input('contract')['contractFrom']);
            $contractRenewal->END_DATE = \Carbon\Carbon::parse($request->input('contract')['contractTo']);
            $contractRenewal->GRADE_ID = $request->input('contract')['grade']['id'];

            $contractRenewal->save();
        }

        if($case_no){
            $pmData = [
                'host_country_id'           =>  $principal->HOST_COUNTRY_ID,
                'host_country_id_label'     =>  $principal->HOST_COUNTRY_ID
            ];

            $response = \Processmaker::updateCaseVariables($case_no, $pmData);
            // dd($response);
        }

        return ['status' => 'success', 'principal'  =>  $principal];
    }

    function update(Request $request){
        // $host_country_id = $request->id;
        // unset($request['id']);
        $principal = Principal::find($request->input('id'));

        $new_path = null;
        if (null != $request->file('image.file')) {
            $new_path = $request->file('image.file')->store('principal_photos');

            \Storage::delete($principal->IMAGE);
        }

        $principal->LAST_NAME = strtoupper($request->input('lastName'));
        $principal->OTHER_NAMES = ucwords(strtolower($request->input('otherNames')));
        $principal->EMAIL = $request->input('email');
        $principal->MOBILE_NO = $request->input('mobileNo');
        $principal->OFFICE_NO = $request->input('officeNo');
        $principal->MARITAL_STATUS = $request->input('maritalStatus');
        $principal->DATE_OF_BIRTH = \Carbon\Carbon::parse($request->input('dob'));
        $principal->GENDER = $request->input('gender');
        $principal->ADDRESS = $request->input('Address');
        $principal->RESIDENCE = $request->input('residence');
        $principal->PLACE_OF_BIRTH = $request->place_of_birth;
        $principal->NATIONALITY = (\App\Models\Country::where('iso_3', $request->nationality['id'])->first())->id;

        $principal->IMAGE = $new_path;

        $principal->PIN_NO = strtoupper($request->pin);
        $principal->R_NO = $request->residenceNo;
        $principal->DL_NO = strtoupper($request->drivingLicense);

        // echo "<pre>";print_r($principal);die;

        $principal->save();

        return Principal::where('HOST_COUNTRY_ID', $principal->HOST_COUNTRY_ID)->with(['contracts', 'dependents', 'passports', 'vehicles'])->first();
    }

    function get(Request $request){
        $principal = Principal::where('HOST_COUNTRY_ID', $request->id)->with(['contracts', 'dependents', 'passports', 'vehicles'])->first();

        // \DB::enableQueryLog();
      // $query = \DB::getQueryLog();
      // dd($query);

        return $principal;
    }

    function addPassport(Request $request){
        $passport = new PrincipalPassport();

        $passport->PRINCIPAL_ID = $request->principal_id;
        $passport->PASSPORT_TYPE_ID = $request->passport_type;
        $passport->PASSPORT_NO = $request->passportNo;
        $passport->PLACE_OF_ISSUE = $request->place_issue;
        $passport->COUNTRY_OF_ISSUE = $request->country_issue['label'];
        $passport->ISSUE_DATE = \Carbon\Carbon::parse($request->date_issue);
        $passport->EXPIRY_DATE = \Carbon\Carbon::parse($request->expiry_date);

        $passport->save();

        return $passport;
    }

    function editPassport(Request $request){
        $passport = PrincipalPassport::find($request->passport_id);

        $passport->PASSPORT_TYPE_ID = $request->passport_type;
        $passport->PASSPORT_NO = $request->passportNo;
        $passport->PLACE_OF_ISSUE = $request->place_issue;
        $passport->COUNTRY_OF_ISSUE = $request->country_issue;
        $passport->ISSUE_DATE = \Carbon\Carbon::parse($request->date_issue);
        $passport->EXPIRY_DATE = \Carbon\Carbon::parse($request->expiry_date);

        $passport->save();

        return $passport;
    }

    function deletePassport(Request $request){
        $passport_id = $request->id;

        $passport = PrincipalPassport::findOrFail($passport_id);

        PrincipalPassport::destroy($passport_id);

        return ['status' => 'success'];
    }

    function addContract(Request $request){
        $principalContract = new PrincipalContract();
        $contractRenewal = new PrincipalContractRenewal();

        $principalContract->PRINCIPAL_ID = $request->principal_id;
        $principalContract->INDEX_NO = $request->indexNo;
        $principalContract->AGENCY_ID = $request->agency['id'];
        $principalContract->DESIGNATION = $request->designation['label'];
        $principalContract->FUNC_TITLE = $request->functionalTitle;
        $principalContract->CONTRACT_TYPE_ID = $request->contractType['id'];

        $principalContract->save();

        $contractId = $principalContract->ID;

        $contractRenewal->CONTRACT_ID = $contractId;
        $contractRenewal->START_DATE = \Carbon\Carbon::parse($request->contractFrom);
        $contractRenewal->END_DATE = \Carbon\Carbon::parse($request->contractTo);
        $contractRenewal->GRADE_ID = $request->grade['id'];

        $contractRenewal->save();

        return PrincipalContract::find($contractId);
    }

    function editContract(Request $request){
        // dd($request->input());
        $principalContract = PrincipalContract::find($request->contract_id);

        $principalContract->INDEX_NO = $request->indexNo;
        $principalContract->AGENCY_ID = $request->agency['id'];
        $principalContract->DESIGNATION = $request->designation['label'];
        $principalContract->FUNC_TITLE = $request->functionalTitle;
        $principalContract->CONTRACT_TYPE_ID = $request->contractType['id'];
        $principalContract->GRADE_ID = $request->grade['id'];
        $principalContract->CONTRACT_FROM = \Carbon\Carbon::parse($request->contractFrom);
        $principalContract->CONTRACT_TO = \Carbon\Carbon::parse($request->contractTo);

        $principalContract->save();

        $contractRenewal = PrincipalContractRenewal::where('CONTRACT_ID', $request->contract_id)->first();
        // echo "<pre>";print_r($contractRenewal);die;

        $contractRenewal->START_DATE = \Carbon\Carbon::parse($request->contractFrom);
        $contractRenewal->END_DATE = \Carbon\Carbon::parse($request->contractTo);
        $contractRenewal->GRADE_ID = $request->grade['id'];

        $contractRenewal->update();

        return PrincipalContract::find($request->contract_id);
    }

    function deleteContract(Request $request){
        $contract_id = $request->id;
        $contract = PrincipalContract::findOrFail($contract_id);
        $renewal = PrincipalContractRenewal::where('CONTRACT_ID', $contract_id);

        $renewal->delete();

        PrincipalContract::destroy($contract_id);

        return ['status' => 'success'];
    }

    function addDependent(Request $request) {
        $principalDependent = new PrincipalDependent();
        $host_country_id = $request->principal_id;

        $dependentFilePath = null;

        if(null !== $request->file('imageFile')){
            $dependentFilePath = $request->file('imageFile')->store('dependent_photos');
        }

        $principalDependent->PRINCIPAL_ID = $host_country_id;
        $principalDependent->LAST_NAME = $request->lastName;
        $principalDependent->OTHER_NAMES = $request->otherNames;
        $principalDependent->RELATIONSHIP_ID = $request->relationshipType['id'];
        $principalDependent->COUNTRY = $request->country['label'];
        $principalDependent->EMPLOYMENT_DETAILS = $request->employment;
        $principalDependent->PASSPORT_NO = $request->passport;
        $principalDependent->DATE_OF_BIRTH = \Carbon\Carbon::parse($request->dob);
        $principalDependent->IMAGE = $dependentFilePath;

        $principalDependent->save();

        $host_country_id = 20000000 + $principalDependent->ID;

        $principalDependent->HOST_COUNTRY_ID = $host_country_id;

        $principalDependent->save();

        return $principalDependent;
    }

    function editDependent(Request $request) {
        $principalDependent = PrincipalDependent::find($request->id);
        $dependentFilePath = null;

        if(null !== $request->file('imageFile')){
            $dependentFilePath = $request->file('imageFile')->store('dependent_photos');
        }

        $principalDependent->LAST_NAME = $request->lastName;
        $principalDependent->OTHER_NAMES = $request->otherNames;
        $principalDependent->RELATIONSHIP_ID = $request->relationshipType['id'];
        $principalDependent->COUNTRY = $request->country['label'];
        $principalDependent->EMPLOYMENT_DETAILS = $request->employment;
        $principalDependent->PASSPORT_NO = $request->passport;
        $principalDependent->DATE_OF_BIRTH = \Carbon\Carbon::parse($request->dob);

        if ($dependentFilePath != null) {
            $principalDependent->IMAGE = $dependentFilePath;
        }

        $principalDependent->save();

        return PrincipalDependent::find($request->id);
    }

    function deleteDependent(Request $request) {
        $dependent_id = $request->id;
        $dependent = PrincipalDependent::findOrFail($dependent_id);

        PrincipalDependent::destroy($dependent_id);

        return ['status' => 'success'];
    }
}
