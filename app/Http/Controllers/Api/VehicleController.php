<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use \App\Models\Ref\VehiclePlatePrefix;
use \App\Models\Ref\VehiclePlatePrefixAgency;

use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    function getVehicles(Request $request){
    	$host_country_id = (isset($request->host_country_id)) ? $request->host_country_id : "";
    	$searchQueries = $request->get('query');
    	$limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');
		
		$vehiclesQuery = \App\Models\Vehicle::with("makeModel", "owners");
		if($host_country_id){
			$vehiclesQuery->whereHas('owners', function($q) use ($host_country_id){
				$q->where('HOST_COUNTRY_ID', $host_country_id);
			});
		}
        $count = $vehiclesQuery->count();

        $vehiclesQuery = $vehiclesQuery->limit($limit)->skip($limit * ($page - 1));

    	
    	return ['data' 	=> $vehiclesQuery->get(), 'count'	=>	$count];
    }

    function getPrefixes(){
        $prefixes = VehiclePlatePrefix::with('agencies')->get();

        return $prefixes;
    }

    function addPrefix(Request $request){
        $validatedData = $request->validate([
            'prefix'    =>  'required|unique:2019.VEHICLE_PLATE_PREFIX',
            'suffix'    =>  'required'
        ]);

        return VehiclePlatePrefix::create(['prefix' =>  $request->prefix, 'suffix'  =>  $request->suffix]);
    }

    function updatePrefix(Request $request){
        $prefix = VehiclePlatePrefix::findOrFail($request->id);

        $validatedData = $request->validate([
            'prefix'    =>  ['required', new \App\Rules\CheckPrefix($prefix->id)]
        ]);

        $prefix->prefix = $request->prefix;

        $prefix->save();
    }

    function addOrganizationPrefix(Request $request){
        $prefixAgency = VehiclePlatePrefixAgency::where('prefix_id', $request->prefix_id)->where('host_country_id', $request->host_country_id)->first();

        if (!$prefixAgency) {
            return VehiclePlatePrefixAgency::create(['prefix_id' => $request->prefix_id, 'host_country_id' => $request->host_country_id]);
        }

        return \Response::json([ 'message' => 'Cannot add organization since it already exists for this prefix'], 500);
    }

    function removeOrganizationPrefix(Request $request){
        // TODO: CHECK WHETHER THERE IS SOME DATA TIED TO THIS
        return VehiclePlatePrefixAgency::destroy($request->id);
    }

    function registerTims(Request $request){
        $tims = new \App\Models\TimsRegistration();

        $tims->HOST_COUNTRY_ID = $request->host_country_id;
        $tims->DIP_ID_NO = $request->diplomatic_id;
        $tims->MOBILE_NO = $request->mobile_no;
        $tims->KRA_PIN_NO = $request->pin;
        $tims->USERNAME = $request->username;
        $tims->PASSWORD  = \Crypt::encrypt($request->password);
        $tims->REGISTRATION_DATE = date('Y-m-d', strtotime($request->registrationDate));

        $tims->save();

        return $tims;
    }

    function searchTims(Request $request){
        $searchQueries = $request->get('normalSearch');
        $agencySearch = $request->get('agencySearch');
        $limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $columns = [
            "HOST_COUNTRY_ID" =>"HOST_COUNTRY_ID",
            "CLIENT" =>"CLIENT",
            "AGENCY" =>"ACRONYM",
            "DIPLOMATIC ID" =>"DIP_ID_NO",
            "USERNAME" =>"USERNAME",
            "KRA_PIN_NO" => "KRA_PIN_NO", 
            "REGISTRATION_DATE" =>"REGISTRATION_DATE"
        ];

        // $queryBuilder = \DB::table("TIMS_REGISTRATION")
        //                     ->select('TIMS_REGISTRATION.HOST_COUNTRY_ID', 'TIMS_REGISTRATION.DIP_ID_NO', 'TIMS_REGISTRATION.MOBILE_NO', 'TIMS_REGISTRATION.USERNAME', 'TIMS_REGISTRATION.KRA_PIN_NO', 'TIMS_REGISTRATION.REGISTRATION_DATE', \DB::raw("(CASE WHEN PRINCIPAL.HOST_COUNTRY_ID IS NOT NULL THEN CONCAT(PRINCIPAL.LAST_NAME, ', ', PRINCIPAL.OTHER_NAMES) WHEN PRINCIPAL_DEPENDENT.HOST_COUNTRY_ID IS NOT NULL THEN CONCAT(PRINCIPAL_DEPENDENT.LAST_NAME, ', ', PRINCIPAL_DEPENDENT.OTHER_NAMES) END) AS CLIENT"))
        //                     ->leftJoin('PRINCIPAL', 'PRINCIPAL.HOST_COUNTRY_ID', '=', 'TIMS_REGISTRATION.HOST_COUNTRY_ID')
        //                     ->leftJoin('PRINCIPAL_DEPENDENT', 'PRINCIPAL_DEPENDENT.HOST_COUNTRY_ID', '=', 'TIMS_REGISTRATION.HOST_COUNTRY_ID');

        $queryBuilder = \App\Models\TimsRegistrationView::select('*');
        if($searchQueries){
            $queryBuilder->where('CLIENT', 'LIKE', "%{$searchQueries}%")
            ->orWhere('DIP_ID_NO', 'LIKE', "%{$searchQueries}%")
            ->orWhere('KRA_PIN_NO', 'LIKE', "%{$searchQueries}%")
            ->orWhere('MOBILE_NO', 'LIKE', "%{$searchQueries}%")
            ->orWhere('USERNAME', 'LIKE', "%{$searchQueries}%")
            ->orWhere('ACRONYM', 'LIKE', "%{$searchQueries}%");
        }

        if($agencySearch){
            $queryBuilder->where('ACRONYM', $agencySearch);
        }

        if ($orderBy) {
            $queryBuilder->orderBy($columns[$orderBy], ($ascending) ? "ASC" : "DESC");
        }

        $count = $queryBuilder->count();

        $queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));
        $registrations = $queryBuilder->get();

        return [
            'count' =>  $count,
            'data'  =>  $registrations
        ];
    }

    function searchRNP(Request $request){
        $searchQueries = $request->get('normalSearch');
        $dateQuery = $request->get('dateSearch');
        $limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $queryBuilder = \App\ReturnedPlate::with('plates', 'plates.client');
        if($searchQueries){
            $queryBuilder = $queryBuilder->whereHas('plates', function($query) use ($searchQueries){
                $number = str_replace(' ', '', $searchQueries);
                return $query->where('PLATE_NO', 'LIKE', "%{$number}%")
                                ->orWhereHas('client', function ($q) use ($searchQueries){
                                    return $q->where('LAST_NAME', 'LIKE', "%{$searchQueries}%")->orWhere('OTHER_NAMES', 'LIKE', "%{$searchQueries}%");
                                });
            });
        }

        if($dateQuery){
            $queryBuilder = $queryBuilder->where('RNP_DATE', new \Carbon\Carbon($dateQuery));
        }

        $count = $queryBuilder->count();
        $queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));

        $lists = $queryBuilder->get();

        return [
            'count' =>  $count,
            'data'  =>  $lists
        ];
    }

    function createRNPList(Request $request){
        $validatedData = $request->validate([
            'rnpDate'  =>  ['required', 'unique:RETURNED_PLATES,RNP_DATE']
        ]);

        $date = $request->input('rnpDate');

        $rnp = new \App\ReturnedPlate();

        $rnp->RNP_DATE = new \Carbon\Carbon($date);

        $rnp->save();

        $rnpId = $rnp->id;
        // $rnpId = 0;

        $list = collect($request->returnedPlates)->map(function($plate) use ($rnpId){
            return [
                'RETURNED_PLATE_ID' =>  $rnpId,
                'HOST_COUNTRY_ID'   =>  $plate['hc_id'],
                'PLATE_NO'          =>  $plate['plateNo'],
                'MEASUREMENTS'      =>  $plate['measurements']
            ];
        })->toArray();

        // dd($list);

        \App\ReturnedPlateList::insert($list);
        $data = \App\ReturnedPlate::with(['plates'])->find($rnp->id);

        return $data;
    }

    function editRNPList(Request $request){
        $rnp = \App\ReturnedPlate::where('id', $request->input('id'))->firstOrFail();

        \App\ReturnedPlateList::where('RETURNED_PLATE_ID', $rnp->id)->delete();

        $rnpId = $rnp->id;

        $list = collect($request->returnedPlates)->map(function($plate) use ($rnpId){
            return [
                'RETURNED_PLATE_ID' =>  $rnpId,
                'HOST_COUNTRY_ID'   =>  $plate['hc_id'],
                'PLATE_NO'          =>  $plate['plateNo'],
                'MEASUREMENTS'      =>  $plate['measurements']
            ];
        })->toArray();

        \App\ReturnedPlateList::insert($list);
        $data = \App\ReturnedPlate::with(['plates'])->find($rnp->id);

        return $data;
    }

    function getRNP(Request $request){
        return \App\ReturnedPlate::where("id", $request->id)->with(['plates'])->first();
    }

    function uploadSignedList(Request $request){
        $rnp = \App\ReturnedPlate::where('id', $request->id)->firstOrFail();

        $file = $request->file('uploadedList');

        $path = $file->store('rnp/lists/signed');

        $rnp->SIGNED_DOCUMENT = $path;

        $rnp->save();

    }

    function downloadSignedList(Request $request){
        $rnp = \App\ReturnedPlate::where('id', $request->id)->firstOrFail();

        if ($rnp->SIGNED_DOCUMENT) {
            return \Storage::download($rnp->SIGNED_DOCUMENT);
        }else{
            abort_404();
        }
    }

    function downloadUnsignedList(Request $request){
        $id = $request->id;

        $rnp = \App\ReturnedPlate::where('id', $id)->firstOrFail();

        // foreach ($rnp->plates as $plate) {
        //     if($plate->clientType == "dependent")
        //         dd($plate->client->principal->latest_contract);
        // }

        $data['data'] = $rnp;
        $data['date'] = date('d F Y');

        $pdf = \PDF::loadView('pdf.rnp', $data);

        return $pdf->stream('Returned_Plates_' . $rnp->RNP_DATE . '.pdf');
    }

    function getVehicleDetails(Request $request){
        $id = $request->id;
        $type = $request->type;
        if($type == "old"){
            $vehicle = \DB::connection('old_pm')->table('unon_sm_vehicle')->where('record_id', $id)->first();
            $makeModel = new \StdClass;
            if ($vehicle) {
                $makeModel = \App\Models\VehicleMakeModel::where('MAKE_MODEL', $vehicle->make_model)->first();
            }
            return [ 'data' => $vehicle, 'make_model'   =>  $makeModel ];
        }else if($type == "new"){
            $vehicle = \DB::table('VW_VEHICLE_OWNER')->where('VEHICLE_ID', $id)->first();
            return [ 'data' => $vehicle ];
        }
    }

    function addPlate(Request $request){
        $plate_number = $request->plate . $request->suffix;
        $validatedData = $request->validate([
            'prefix'    =>  'required',
            'plate'     =>  [
                'required'
            ],
            'suffix'    =>  'required'
        ]);

        $plates = \App\Models\Ref\VehiclePlate::where('plate_number', $plate_number)->where('prefix_id', $request->prefix)->exists();

        if (!$plates) {
            $newPlate = \App\Models\Ref\VehiclePlate::create([
                'prefix_id'     =>  $request->prefix['id'],
                'plate_number'  =>  $plate_number,
                'suffix'        =>  $request->suffix,
                'status'        =>  0
            ]);

            return $newPlate;
        }

        return response()->json([
            'message'   =>  'The plate number already exists'
        ], 422);
    }

    function bulkPlates(Request $request){
        $validatedData = $request->validate([
            'plates'             =>  'required',
            'plates.*.prefix'    =>  'required',
            'plates.*.suffix'    =>  'required',
            'plates.*.start'     =>  'required|numeric|min:1|max:998',
            'plates.*.end'       =>  'required|numeric|min:2|max:999|gt:plates.*.start'
        ]);

        try{
            foreach ($request->plates as $plate) {
                for ($i = $plate['start']; $i <= $plate['end'] ; $i++) { 
                    $plateExists = \App\Models\Ref\VehiclePlate::where('plate_number', $i . $plate['suffix'])->where('prefix_id', $plate['prefix']['id'])->exists();

                    if (!$plateExists) {
                        $newPlate = new \App\Models\Ref\VehiclePlate();
                        
                        $newPlate->prefix_id = $plate['prefix']['id'];
                        $newPlate->suffix = $plate['suffix'];
                        $newPlate->plate_number = $i . $plate['suffix'];
                        $newPlate->status = 0;

                        $newPlate->save();
                    }
                }
            }
        }catch(\Exception $ex){
            return response()->json(["message" => "Could not process the request at the moment", "errors" => $ex->errorInfo], 422);
        }
    }

    function getPlatesList(Request $request){
        $searchQueries = $request->get('normalSearch');
        $limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $queryBuilder = \App\Models\Ref\VehiclePlate::with('prefix', 'prefix.agencies', 'client', 'client.principal', 'client.dependent', 'client.agency', 'client.dependent.principal');
        if ($searchQueries) {
            $queryBuilder->where('plate_number', $searchQueries);
            $queryBuilder->orWhereHas('prefix', function($query) use ($searchQueries){
                $prefix = str_replace(" ", "", $searchQueries);
                return $query->where('prefix', 'LIKE', "%{$prefix}%")
                                ->orWhere(\DB::Raw('CONCAT(VEHICLE_PLATE_PREFIX.prefix, VEHICLE_PLATES.plate_number)'), 'LIKE', "%{$prefix}%")
                                ->orWhereHas('agencies.agency', function($q) use ($searchQueries){
                                    return $q->where('ACRONYM', 'LIKE', "%{$searchQueries}%")
                                                ->orWhere('AGENCYNAME', 'LIKE', "%{$searchQueries}%");
                                });
            });

            $queryBuilder->orWhereHas('client', function($query) use ($searchQueries){
                $query->from('pm_master_data.VEHICLE_OWNER');
                return $query->whereHas('principal', function($q) use ($searchQueries){
                    $q->from('pm_master_data.PRINCIPAL');
                    return $q->where('LAST_NAME', 'LIKE', "%{$searchQueries}%")
                                ->orWhere('OTHER_NAMES', 'LIKE', "%{$searchQueries}%");
                })
                ->orWhereHas('dependent', function($q) use ($searchQueries){
                     $q->from('pm_master_data.PRINCIPAL_DEPENDENT');
                    return $q->where('LAST_NAME', 'LIKE', "%{$searchQueries}%")
                                ->orWhere('OTHER_NAMES', 'LIKE', "%{$searchQueries}%")
                                ->orWhereHas('principal', function($pq) use ($searchQueries){
                                    $pq->from('pm_master_data.PRINCIPAL');
                                    return $pq->where('LAST_NAME', 'LIKE', "%{$searchQueries}%")
                                                ->orWhere('OTHER_NAMES', 'LIKE', "%{$searchQueries}%");
                                    });
                })
                ->orWhereHas('agency', function($q) use ($searchQueries){
                    return $q->where('ACRONYM', 'LIKE', "%{$searchQueries}%")
                                ->orWhere('AGENCYNAME', 'LIKE', "%{$searchQueries}%");
                });
            });

        }

        $count = $queryBuilder->count();
        $queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));

        $lists = $queryBuilder->get();

        return [
            'count' =>  $count,
            'data'  =>  $lists
        ];
    }

    function searchFormA(Request $request){
        $case = $request->input('case');

        // Get details of Form A and Vehicle
        $formA = \App\Models\FormA::where('CASE_NO', $case)->with('vehicle')->firstOrFail();

        return $formA;
    }

    function createLogbookCase(Request $request){
        $form_a = $request->form_a;

        $assignedPlate = $request->input('assignedPlate');

        $formA = \App\Models\FormA::where('CASE_NO', $form_a)->with('vehicle')->firstOrFail();

        $variables = [[
            'host_country_id'   =>  $formA->HOST_COUNTRY_ID,
            'client_name'       =>  $formA->client_details->name,
            'pro1b_case_no'     =>  $formA->PRO_1B_CASE_NO,
            'form_a_case_no'    =>  $formA->CASE_NO,
            'assigned_plate'    =>  $assignedPlate
        ]];

        $user = "00000000000000000000000000000001";
        $task = "3392311385edf4544853db8009703611";
        $pro_uid = "7279291495edf3a5356d477046323760";

        $data = [
            'variables' =>  $variables,
            'pro_uid'   =>  $pro_uid,
            'tas_uid'   =>  $task,
            'usr_uid'   =>  $user
        ];
        try{
            $url = "https://".env('PM_SERVER_DOMAIN')."/api/1.0/workflow/cases";

            $authenticationData = json_decode(\Storage::get("pmauthentication.json"));

            $response = \Processmaker::executeREST($url, "POST", $data, $authenticationData->access_token);

            if ($response->app_uid) {
                $routeRes = \Processmaker::routeCase($response->app_uid);
            }

            $case_no = $response->app_number;

            $formA->PLATE_NO = $assignedPlate;
            $formA->LOGBOOK_CASE_NO = $case_no;

            $formA->save();

            return [
                'logbook_case_no'   =>  $case_no
            ];
        }catch(\Exception $ex){
            return response()->json([
                'message'   =>  $ex->getMessage()], 400);
        }
    }
}
