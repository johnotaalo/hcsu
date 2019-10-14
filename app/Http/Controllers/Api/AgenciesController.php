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
    	return Agency::where('ACRONYM', 'LIKE', "%{$searchTerm}%")->orWhere('AGENCYNAME', 'LIKE', '%{$searchTerm}%')->get();
    }

    function addAgencies(Request $request){
    	$uploadPath = $request->file('agencyDetails.agency_hca')->store('hca');
    	$data = [
			'ACRONYM'			=>	$request->input('agencyDetails.agency_acronym'),
			'AGENCYNAME'		=>	$request->input('agencyDetails.agency_name'),
			'POBOX'				=>	$request->input('agencyDetails.agency_pobox'),
			'LOCATION'			=>	$request->input('agencyDetails.agency_location'),
			'PHY_ADDRESS'		=>	$request->input('agencyDetails.agency_physical_address'),
			'HCA'				=>	$uploadPath,
			'PIN_NO'			=>	$request->input('agencyDetails.agency_pin'),
			'POSTCODE'			=>	$request->input('agencyDetails.agency_postal_code')
		];

		$agency = Agency::create($data);
		$host_country_id = 0;
		$host_country_id = 3000000 + $agency->AGENCY_ID;
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
		dd($res);
    }

    function updateAgency(Request $request){
    	$data = [
			'ACRONYM'			=>	$request->input('agencyDetails.agency_acronym'),
			'AGENCYNAME'		=>	$request->input('agencyDetails.agency_name'),
			'POBOX'				=>	$request->input('agencyDetails.agency_pobox'),
			'LOCATION'			=>	$request->input('agencyDetails.agency_location'),
			'PHY_ADDRESS'		=>	$request->input('agencyDetails.agency_physical_address'),
			'PIN_NO'			=>	$request->input('agencyDetails.agency_pin'),
			'POSTCODE'			=>	$request->input('agencyDetails.agency_postal_code')
		];

		$agency = Agency::where('HOST_COUNTRY_ID', $request->agency_id)->firstOrFail();
		foreach ($data as $key => $value) {
			$agency->$key = $value;
		}

		$agency->save();
    	// return $request->input;
    	if($request->input('focalPoints')){
	    	if ($request->input('focalPoints')) {
	    		foreach($request->input('focalPoints') as $fp){
	    			if (!isset($fp['ID'])) {
	    				$username = $this->generateUsername($fp['other_names'], $fp['last_name'], $fp['index_no']);
	    				$fpdata = [
							"AGENCY_HOST_COUNTRY_ID"	=>	$request->agency_id,
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

						$res = Notification::send($createdFP, $notification);
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
}
