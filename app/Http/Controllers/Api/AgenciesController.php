<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\AgencyFocalPoint;
use App\Notifications\FocalPointPassword;
use Notification;
use Illuminate\Support\Facades\Password;

class AgenciesController extends Controller
{

    function searchAgencies(Request $request){
    	$searchTerm = $request->query('q');
    	$focalpoint = $request->query('focalpoint');
    	if (!$focalpoint){
            return Agency::where('IS_ACTIVE', true)->where('ACRONYM', 'LIKE', "%{$searchTerm}%")->orWhere('AGENCYNAME', 'LIKE', '%{$searchTerm}%')->with("focalPointMapping")->get();
        }
    	else{
    	    $agencyMapping = (\App\Models\AgencyFocalPoint::find($focalpoint))->agencies->pluck('AGENCY_ID')->toArray();
            return Agency::where('IS_ACTIVE', true)->whereIn('AGENCY_ID', $agencyMapping)->where('ACRONYM', 'LIKE', "%{$searchTerm}%")->orWhere('AGENCYNAME', 'LIKE', '%{$searchTerm}%')->get();
        }
    }

    function addAgencies(Request $request){
    	// dd($request->files);
    	$request->validate([
    		'agencyDetails.agency_acronym'				=>	'required|unique:2019.agencies,ACRONYM',
    		'agencyDetails.agency_name'					=>	'required|unique:2019.agencies,AGENCYNAME',
    		'agencyDetails.agency_pobox'				=>	'required',
			'agencyDetails.agency_location'				=>	'required',
			'agencyDetails.agency_physical_address'		=>	'required',
			'agencyDetails.agency_pin'					=>	'required',
			'agencyDetails.agency_postal_code'			=>	'required',
			'agencyDetails.agency_hca'					=>	'nullable|mimes:pdf',
			'agencyDetails.image.file'					=>	'nullable|mimes:jpeg,png,jpg'
    	]);

    	$uploadPath = null;
    	if($request->hasFile('agencyDetails.agency_hca')){
    		$uploadPath = $request->file('agencyDetails.agency_hca')->store('hca');
    	}

    	$logoLink = "";

    	if ($request->hasFile('agencyDetails.image.file')) {
    		$logoLink = $request->file('agencyDetails.image.file')->store('agencyLogos');
    	}

    	$data = [
			'ACRONYM'			=>	$request->input('agencyDetails.agency_acronym'),
			'AGENCYNAME'		=>	$request->input('agencyDetails.agency_name'),
			'POBOX'				=>	$request->input('agencyDetails.agency_pobox'),
			'LOCATION'			=>	$request->input('agencyDetails.agency_location'),
			'PHY_ADDRESS'		=>	$request->input('agencyDetails.agency_physical_address'),
			'HCA'				=>	$uploadPath,
			'PIN_NO'			=>	$request->input('agencyDetails.agency_pin'),
			'POSTCODE'			=>	$request->input('agencyDetails.agency_postal_code'),
			'logo_link'			=>	$logoLink
		];

		$agency = Agency::create($data);
		$host_country_id = 0;
		$host_country_id = 30000000 + $agency->AGENCY_ID;
		$agency->HOST_COUNTRY_ID = $host_country_id;
		$agency->save();

		if($request->input('focalPoints')){
			$fpdata  = [];
			foreach ($request->input('focalPoints') as $fp) {
				// echo "<pre>";print_r($fp['last_name']);die;
				$username = $this->generateUsername($fp['other_names'], $fp['last_name'], $fp['index_no']);
				$fpdata = [
					"AGENCY_HOST_COUNTRY_ID"	=>	$host_country_id,
					"INDEX_NO"					=>	$fp['index_no'],
					"LAST_NAME"					=>	$fp['last_name'],
					"OTHER_NAMES"				=>	$fp['other_names'],
					"EXTENSION"					=>	$fp['extension'],
					"MOBILE_NO"					=>	$fp['mobile_no'],
					"EMAIL"						=>	$fp['email_address'],
					"USERNAME"					=>	$username,
					"PASSWORD"					=>	str_random(20)
				];

				$createdFP = AgencyFocalPoint::create($fpdata);
				$user = \App\User::create([
					'name'		=>	$createdFP->full_name,
					'email'		=>	$createdFP->EMAIL,
					'password'	=>	$createdFP->PASSWORD,
					'username'	=>	$createdFP->USERNAME,
					'user_type'	=>	\App\Enums\UserType::FocalPoint,
					'ext_id'	=>	$createdFP->ID
				]);

				$user->save();

				$token = app(\Illuminate\Auth\Passwords\PasswordBroker::class)->createToken($user);

				$notification = new FocalPointPassword($createdFP, $token);

				Notification::send($createdFP, $notification);
			}
		}

		return $agency;
    }

    function sendResetPassword(Request $request){
    	$user = \App\User::find($request->id);
    	$focalpoint = $user->focal_point;

    	// dd($focalpoint);

    	$token = app(\Illuminate\Auth\Passwords\PasswordBroker::class)->createToken($user);

		$notification = new FocalPointPassword($focalpoint, $token);
		// dd($notification);
		$res = Notification::send($user, $notification);
    }

    function updateAgency(Request $request){
    	$request->validate([
    		// 'agencyDetails.agency_acronym'				=>	"required|unique:2019.agencies,ACRONYM,{$request->agency_id},AGENCY_ID",
    		// 'agencyDetails.agency_name'					=>	"required|unique:2019.agencies,AGENCYNAME,{$request->agency_id},AGENCY_ID",
    		'agencyDetails.agency_pobox'				=>	'required',
			'agencyDetails.agency_location'				=>	'required',
			'agencyDetails.agency_physical_address'		=>	'required',
			'agencyDetails.agency_pin'					=>	'required',
			'agencyDetails.agency_postal_code'			=>	'required',
			'agencyDetails.agency_hca'					=>	'nullable|mimes:pdf',
			'agencyDetails.image.file'					=>	'nullable|mimes:jpeg,png,jpg'
    	]);

    	$data = [
			'ACRONYM'			=>	$request->input('agencyDetails.agency_acronym'),
			'AGENCYNAME'		=>	$request->input('agencyDetails.agency_name'),
			'POBOX'				=>	$request->input('agencyDetails.agency_pobox'),
			'LOCATION'			=>	$request->input('agencyDetails.agency_location'),
			'PHY_ADDRESS'		=>	$request->input('agencyDetails.agency_physical_address'),
			'PIN_NO'			=>	$request->input('agencyDetails.agency_pin'),
			'POSTCODE'			=>	$request->input('agencyDetails.agency_postal_code'),
		];

    	$uploadPath = null;
    	if($request->hasFile('agencyDetails.agency_hca')){
    		$uploadPath = $request->file('agencyDetails.agency_hca')->store('hca');
    		$data['HCA'] = $uploadPath;
    	}

    	$logoLink = "";

    	if ($request->hasFile('agencyDetails.image.file')) {
    		$logoLink = $request->file('agencyDetails.image.file')->store('agencyLogos');
    		$data['logo_link'] = $logoLink;
    	}

		// dd($request->input('focalPoints'));

		$agency = Agency::where('HOST_COUNTRY_ID', $request->agency_id)->firstOrFail();
		foreach ($data as $key => $value) {
			$agency->$key = $value;
		}

		$agency->save();
    	// return $request->input;
    	if ($request->input('focalPoints')) {
    		foreach($request->input('focalPoints') as $fp){
    			$fpdata = [
					"AGENCY_HOST_COUNTRY_ID"	=>	$request->agency_id,
					"INDEX_NO"					=>	$fp['index_no'],
					"LAST_NAME"					=>	$fp['last_name'],
					"OTHER_NAMES"				=>	$fp['other_names'],
					"EXTENSION"					=>	$fp['extension'],
					"MOBILE_NO"					=>	$fp['mobile_no'],
					"EMAIL"						=>	$fp['email_address']
				];

    			if (!isset($fp['id'])) {
    				$username = $this->generateUsername($fp['other_names'], $fp['last_name'], $fp['index_no']);

    				$fpdata["USERNAME"] =	$username;
					$fpdata["PASSWORD"] =	str_random(20);

					$createdFP = AgencyFocalPoint::create($fpdata);
					$user = \App\User::create([
						'name'		=>	$createdFP->full_name,
						'email'		=>	$createdFP->EMAIL,
						'password'	=>	$createdFP->PASSWORD,
						'username'	=>	$createdFP->USERNAME,
						'user_type'	=>	\App\Enums\UserType::FocalPoint,
						'ext_id'	=>	$createdFP->ID
					]);

					$user->save();

					$token = app(\Illuminate\Auth\Passwords\PasswordBroker::class)->createToken($user);

					$notification = new FocalPointPassword($createdFP, $token);

					$res = Notification::send($createdFP, $notification);
    			}else{
    				if ($fp['action'] == 'edit') {
    					$focalPoint = AgencyFocalPoint::where('id', $fp['id'])->update($fpdata);
    				}else{
    					$focalPoint = AgencyFocalPoint::findOrFail($fp['id']);

    					$id = $focalPoint->ID;

    					$user = \App\User::where('ext_id', $id)->firstOrFail();

    					$applications = \App\Models\UserApplications::where('SUBMITTED_BY', $user->id)->where('AUTHENTICATION_SOURCE', 'USER')->count();

    					if ($applications == 0) {
    						$focalPoint->delete();
    						$user->delete();
    					}
    				}

    			}
    		}
    	}

    }

    function getAgency(Request $request){
    	$host_country_id = $request->agency_id;

    	return Agency::where('HOST_COUNTRY_ID', $host_country_id)->with('focalPoints')->first();
    }

    function generateUsername($othernames, $lastname, $index_no = null){
		$username = "";
		$firstLetter = $othernames[0];
		$lastname = str_replace(" ", "", $lastname);

		$temp_usernames[] = strtolower($firstLetter . $lastname);
		$temp_usernames[] = strtolower(explode(" ", $othernames)[0]  . $lastname[0]);
		$temp_usernames[] = strtolower($lastname);
		$temp_usernames[] = strtolower(str_replace(" ", "", $othernames));
		$temp_usernames[] = strtolower($lastname . str_replace(" ", "", $othernames));
		$othernamesFrags = explode(" ", $othernames);
		// echo "<pre>";print_r($othernamesFrags);die;
		if(count($othernamesFrags) > 1){
			if (count($othernamesFrags) == 2) {
				$temp_usernames[] = strtolower(str_replace(" ", "", $othernames));
			}else{
				$random_name_1 = array_rand($othernamesFrags);
				$random_name_2 = array_rand($othernamesFrags);
				while($random_name_2 == $random_name_1){
					$random_name_2 = array_rand($othernamesFrags);
				}

				$temp_usernames[] = strtolower($othernamesFrags[$random_name_1] . $othernamesFrags[$random_name_2]);
			}
		}

		if($index_no){
			$temp_usernames[] = $index_no;
		}

		shuffle($temp_usernames);

		$usernameExists = true;
		$key = 0;

		while($usernameExists == true){
			$usernameExists = $this->checkUsername( $temp_usernames[$key] );
			if(!$usernameExists){
				$username = $temp_usernames[$key];
			}
			$key++;
		}

		return $username;
	}

	function checkUsername($username){
		$usernameExists = AgencyFocalPoint::where('USERNAME', $username)->exists();

		return $usernameExists;
	}

	function getFocalPoints(Request $request){
		$agency = Agency::where('HOST_COUNTRY_ID', $request->host_country_id)->firstOrFail();

		return $agency->focalPoints;
	}

	function all(Request $request){
		$searchQueries = $request->get('normalSearch');
        $statusQuery = $request->get('statusSearch');
        $processQuery = $request->get('processSearch');
        $limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $queryBuilder = Agency::select("*");
        if ($searchQueries) {
	        $queryBuilder->where('AGENCYNAME', 'LIKE', "%{$searchQueries}%")
	        				->orWhere('ACRONYM', 'LIKE', "%{$searchQueries}%");
	    }

	    if ($orderBy) {
	    	$columns = [
	    		'HOST_COUNTRY_ID'	=>	'HOST_COUNTRY_ID',
	    		'ACRONYM'			=>	'ACRONYM',
	    		'AGENCY NAME'		=>	'AGENCYNAME'
	    	];

	    	$queryBuilder->orderBy($columns[$orderBy], ($ascending == 1) ? 'ASC' : 'DESC');
	    }

	    $count = $queryBuilder->count();
	    $queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));
	    $data = $queryBuilder->get();

	    return [
    		'count'	=>	$count,
    		'data'	=>	$data
    	];
	}

	function getAllFocalpoints(Request $request){
        $count = 0;
        $data = [];

        $searchQueries = $request->get('normalSearch');
        $limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $queryBuilder = AgencyFocalPoint::select('*')->with('agencies');
        if ($searchQueries){
            $queryBuilder->where('LAST_NAME', 'LIKE', "%{$searchQueries}%")
                            ->orwhere('OTHER_NAMES', 'LIKE', "%{$searchQueries}%")
                                ->orwhere('EMAIL', 'LIKE', "%{$searchQueries}%");
        }

        $count = $queryBuilder->count();
        $queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));
        $data = $queryBuilder->get();

        return [
            'count' =>  $count,
            'data'  =>  $data
        ];
    }

    function storeMapping(Request $request){
        $id = $request->id;
        $agencies = $request->agencies;
        $data = [];

        foreach ($agencies as $agency){
            $mapping = \App\Models\AgencyFocalpointMapping::firstOrNew([
                "FOCAL_POINT_ID"    =>  $id,
                "AGENCY_ID"         =>  $agency["id"]
            ]);

            $mapping->save();
        }

        return [
            'type'      => "Success",
            'message'   =>  'Successfully updated mapping'
        ];
    }

    function removeMapping(Request $request){
        $focal_point_id = $request->focal_point_id;
        $agency_id = $request->agency_id;

        return \App\Models\AgencyFocalpointMapping::where(['FOCAL_POINT_ID' => $focal_point_id, 'AGENCY_ID'    =>  $agency_id])->delete();
    }
}
