<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use \App\Models\Ref\VehiclePlatePrefix;
use \App\Models\Ref\VehiclePlatePrefixAgency;

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
        $limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $queryBuilder = \DB::table("TIMS_REGISTRATION")
                            ->select('TIMS_REGISTRATION.HOST_COUNTRY_ID', 'TIMS_REGISTRATION.DIP_ID_NO', 'TIMS_REGISTRATION.MOBILE_NO', 'TIMS_REGISTRATION.USERNAME', 'TIMS_REGISTRATION.KRA_PIN_NO', 'TIMS_REGISTRATION.REGISTRATION_DATE', \DB::raw("(CASE WHEN PRINCIPAL.HOST_COUNTRY_ID IS NOT NULL THEN CONCAT(PRINCIPAL.LAST_NAME, ', ', PRINCIPAL.OTHER_NAMES) WHEN PRINCIPAL_DEPENDENT.HOST_COUNTRY_ID IS NOT NULL THEN CONCAT(PRINCIPAL_DEPENDENT.LAST_NAME, ', ', PRINCIPAL_DEPENDENT.OTHER_NAMES) END) AS CLIENT"))
                            ->leftJoin('PRINCIPAL', 'PRINCIPAL.HOST_COUNTRY_ID', '=', 'TIMS_REGISTRATION.HOST_COUNTRY_ID')
                            ->leftJoin('PRINCIPAL_DEPENDENT', 'PRINCIPAL_DEPENDENT.HOST_COUNTRY_ID', '=', 'TIMS_REGISTRATION.HOST_COUNTRY_ID');
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
                'HOST_COUNTRY_ID'   =>  $plate['id'],
                'PLATE_NO'          =>  $plate['plateNo'],
                'MEASUREMENTS'      =>  $plate['measurements']
            ];
        })->toArray();

        // dd($list);

        \App\ReturnedPlateList::insert($list);
        $data = \App\ReturnedPlate::with(['plates'])->find($rnp->id);

        return $data;
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
            $file = \Storage::download($rnp->SIGNED_DOCUMENT);
            return response()->download(storage_path("app/rnp/{$filename}"));
        //     $file = \Storage::get($rnp->SIGNED_DOCUMENT);
        //     $type = \Storage::mimeType($file);

        //     $response = Response::make($file, 200);
        //     $response->header("Content-Type", $type);

        // return $response;
            // return;
        }else{abort_404();}
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
            return [ 'data' => $vehicle ];
        }else if($type == "new"){
            $vehicle = \DB::table('VW_VEHICLE_OWNER')->where('VEHICLE_ID', $id)->first();
            return [ 'data' => $vehicle ];
        }
    }
}
