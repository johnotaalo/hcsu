<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;

class SampleController extends Controller
{
    function  index(){
	// $token = $this->getAccessToken();
	// die($token);
	$server = "http://10.104.104.87/api/1.0/workflow/cases/advanced-search";
	$ch = curl_init($server);
	$authVar = json_decode(Storage::get("pmauthentication.json"));
	// echo date('d/m/Y h:i a', $authVar->expiry);
	// echo "<pre>"; print_r($authVar);die;
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $authVar->access_token));
// echo (env('PM_AUTHENTICATION_CODE'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$aCases = json_decode(curl_exec($ch));
	$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	echo $statusCode;
	echo "<pre>";print_r($aCases);   
    }

    function pmauth(Request $request){
	// echo "<pre>";print_r($request);die;
	if(!empty($request->query('error'))){
		die("There was an error getting the access token");
	}
	$postParams = array(
  		 'grant_type'    => 'authorization_code',
   		'code'          => $request->query('code'),
   		'client_id'     => env('PM_CLIENT_ID'),
   		'client_secret' => env('PM_CLIENT_SECRET')
	);
	
	$ch = curl_init("http://10.104.104.87/workflow/oauth2/token");
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
	$result = json_decode(curl_exec($ch));
	$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ($httpStatus != 200) {
		print "Error in HTTP status code: $httpStatus\n";
	}
	elseif (isset($result->error)) {
  		 print "<pre>Error logging into $pmServer:\n" .
		"Error:       {$result->error}\n" .
		"Description: {$result->error_description}\n</pre>";
	}else {
		$result->expiry = time() + $result->expires_in;
		Storage::disk('local')->put('pmauthentication.json', json_encode($result));
		die("Successfully generated and saved token");
	}
	die($request->query('code'));	
    }

    function getAccessToken(){
	$params = array(
        	'grant_type' => 'password',
        	'scope' => '*',
        	'client_id' => env('PM_CLIENT_ID'),
         	'client_secret' => env('PM_CLIENT_SECRET'),
         	'username' => env('PM_USER_NAME'),
         	'password' => env('PM_USER_PASS')
    	);
	
	$url = 'http://10.104.104.87/workflow/oauth2/token';
	// echo "<pre>";print_r($params);die;
	$data = $this->executeRest($url, 'POST', $params);
	echo "<pre>";print_r($data);die;
	return $data['access_token'];
    }

    function executeREST($url, $method = 'GET', $data = array(), $accessToken = ''){
 
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
 
    if($accessToken != ''){
      curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $accessToken));
    }
 
    if($method == 'POST'){
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    }
 
    if($method == 'PUT'){
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    }
 
    if($method == 'DELETE'){
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
    }
// die($method);
 
    $curl_response = curl_exec($curl);
	if(!$curl_response){
		$error = curl_error($curl);
		echo "<pre>";print_r($error);die;
	}
    $decoded = json_decode($curl_response, true);
echo "<pre>";print_r($curl_response);die;    
curl_close($curl);
 
    if( isset($decoded['error']) ) {
      echo 'Error during CURL request: ' . $decoded['error']['message'];
      die;
    } else {
      return $decoded;
    }
     
  }
}
