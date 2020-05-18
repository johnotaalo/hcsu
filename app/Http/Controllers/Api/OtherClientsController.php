<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\OtherClient;

class OtherClientsController extends Controller
{
	function index(Request $request){
		$data = [];

		$searchQueries = $request->get('normalSearch');
        $limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $queryBuilder = \DB::table('other_clients')
        	->select('other_clients.HOST_COUNTRY_ID', 'other_clients.LAST_NAME', 'other_clients.OTHER_NAMES', 'agencies.AGENCYNAME', 'other_clients.PASSPORT_NO', 'agencies.ACRONYM','other_clients.TYPE', \DB::raw('country.name AS NATIONALITY'))
        	->join('pm_ref_data.agencies', 'agencies.AGENCY_ID', 'other_clients.AFFILIATED_AGENCY')
        	->join('pm_ref_data.country', 'country.id', "other_clients.NATIONALITY");

        if($searchQueries){
            $queryBuilder->where('other_clients.LAST_NAME', 'LIKE', "%{$searchQueries}%");
            $queryBuilder->orWhere('other_clients.OTHER_NAMES', 'LIKE', "%{$searchQueries}%");
            $queryBuilder->orWhere('other_clients.HOST_COUNTRY_ID', 'LIKE', "%{$searchQueries}%");
            $queryBuilder->orWhere('other_clients.PASSPORT_NO', 'LIKE', "%{$searchQueries}%");
            $queryBuilder->orWhere('agencies.AGENCYNAME', 'LIKE', "%{$searchQueries}%");
            $queryBuilder->orWhere('country.name', 'LIKE', "%{$searchQueries}%");
        }

        $count = $queryBuilder->count();

        $queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));
    	$clients = $queryBuilder->get();

    	$data = [
    		'data'	=>	$clients,
    		'count'	=>	$count
    	];

		return $data;
	}

	function store(Request $request){
		$client = new OtherClient;

		$client->LAST_NAME = $request->lastName;
		$client->OTHER_NAMES = $request->otherNames;
		$client->NATIONALITY = $request->nationality['id'];
		$client->DATE_OF_BIRTH = $request->dob;
		$client->TYPE = $request->clientType;
		$client->AFFILIATED_AGENCY = $request->agency['id'];
		$client->DESCRIPTION = $request->description;
		$client->PASSPORT_NO = $request->passport['no'];
		$client->ISSUE_DATE = $request->passport['issue_date'];
		$client->EXPIRY_DATE = $request->passport['expiry_date'];
		$client->COUNTRY_OF_ISSUE = $request->passport['country']['id'];
		$client->HOST_COUNTRY_ID = 111;

		$client->save();

		$client->HOST_COUNTRY_ID = $this->generateHostCountryId($client);

		$client->save();

		return $client;
	}

	function getClient(Request $request){
		return OtherClient::where('HOST_COUNTRY_ID', $request->host_country_id)->with(['agency', 'nationality', 'passportCountry'])->firstOrFail();
	}

	function update(Request $request){
		$client = OtherClient::where('HOST_COUNTRY_ID', $request->host_country_id)->firstOrFail();

		$client->LAST_NAME = $request->lastName;
		$client->OTHER_NAMES = $request->otherNames;
		$client->NATIONALITY = $request->nationality['id'];
		$client->DATE_OF_BIRTH = $request->dob;
		$client->TYPE = $request->clientType;
		$client->AFFILIATED_AGENCY = $request->agency['id'];
		$client->DESCRIPTION = $request->description;
		$client->PASSPORT_NO = $request->passport['no'];
		$client->ISSUE_DATE = $request->passport['issue_date'];
		$client->EXPIRY_DATE = $request->passport['expiry_date'];
		$client->COUNTRY_OF_ISSUE = $request->passport['country']['id'];

		$client->save();

		return $client;
	}

	function generateHostCountryId(OtherClient $client){
		$prefix = 60000000;

		return $prefix + $client->id;
	}
}
