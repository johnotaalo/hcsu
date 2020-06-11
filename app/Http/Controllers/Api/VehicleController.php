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
}
