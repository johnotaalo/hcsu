<?php

namespace App\Helpers\HCSU;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Storage;
use App;

class ProcessMaker {
	public static function executeREST($url, $method="GET", $data = [], $accessToken = '', $useCurl = false){
		// die("Execute rest called");
		$authenticationData = json_decode(Storage::get("pmauthentication.json"));
		$tokenExpiry = $authenticationData->expiry;
		\Log::debug("Checking token expiry...");
		if($tokenExpiry < time()){
			\Log::debug("Token ({$authenticationData->access_token}) has expired. Getting another token");
			$accessToken = (new self)->refreshToken();
			if (!$accessToken) {
				\Log::error("Could not refresh token");
				return ['error' => 'There was an error'];	
			}
		}else{
			\Log::debug("Token is okay. Proceeding...");
		}
		
		$params = [];

		$client = new Client([
			'verify' => false
		]);

		if($accessToken != ''){
			$params['headers'] = [
				'Authorization'	=>	'Bearer ' . $accessToken,
				'Accept'		=>	'application/json'
			];
		}

		if (!empty($data)) {
			$params['form_params'] = $data;
		}

		try{
			if(!$useCurl){
				$res = $client->request($method, $url, $params);
				return json_decode($res->getBody());
			}else{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken));
				// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$method = strtoupper($method);

				switch ($method) {
					case "GET":
						break;
					case "DELETE":
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
						break;
					case "PUT":
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
						curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					case "POST":
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
						break;
					default:
						throw new Exception("Error: Invalid HTTP method '$method' $endpoint");
						return null;
				}

				$oResponse = json_decode(curl_exec($ch));
				$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				// die("Sample: " . $httpStatus);
				\Log::debug("Status code: {$httpStatus} for {$url}");

				curl_close($ch);

				return $oResponse;
			}
			// die($res->getStatusCode());
			
		}catch(ClientErrorResponseException $exception){
			return $exception->getResponse()->getBody(true);
		}
	}

	public static function routeCase($app_uid){
		$url = "https://".env('PM_SERVER_DOMAIN')."/api/1.0/workflow/cases/{$app_uid}/route-case";
		
		$authenticationData = json_decode(\Storage::get("pmauthentication.json"));
		return Self::executeREST($url, "PUT", [], $authenticationData->access_token);
	}

	public static function getCaseInformation($case_no){
		if (App::environment('staging')) {
        	$url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no;
        }else{
        	$url = "https://" . env("PM_SERVER_DOMAIN") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no;
        }
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $response = Self::executeREST($url, "GET", NULL, $authenticationData->access_token);
        // dd($response);

        return $response;
    }

    public static function uploadGeneratedForm($case_no, $task_id, $input_document, $localFile){
        // $inputDocuments = $this->getGeneratedDocuments($case_no);
        if (App::environment('staging')) {
        	$url = "http://" . env("PM_SERVER") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no . "/input-document";
        }else{
        	$url = "https://" . env("PM_SERVER_DOMAIN") . "/api/1.0/" . env("PM_WORKSPACE") . "/cases/" . $case_no . "/input-document";
        }
        
        $authenticationData = json_decode(Storage::get("pmauthentication.json"));
        $form = storage_path('app/'. $localFile);
        $fx = new \CurlFile( $form );
        // dd($fx);
        // $form = Storage::get($localFile);
        $data = [
            'inp_doc_uid'       =>  $input_document,
            'tas_uid'           =>  $task_id,
            'app_doc_comment'   =>  "Document generated and uploaded on: " . date('F d, Y H:i'),
            'form'              =>  new \CurlFile( $form )
        ];

        \Log::debug("Data Sent: " . json_encode($data));

        $response = Self::executeREST($url, "POST", $data, $authenticationData->access_token, true);
        \Log::debug("ProcessMaker upload documents: " . json_encode($response));

        return $response;
    }

	private function refreshToken(){
		$authenticationData = json_decode(Storage::get("pmauthentication.json"));
		$server = (App::environment('staging')) ? env('PM_SERVER') : "https://" . env("PM_SERVER_DOMAIN");
		$workspace = env("PM_WORKSPACE");
		$client_id = env("PM_CLIENT_ID");
		$client_secret = env("PM_CLIENT_SECRET");

		$refreshToken = $authenticationData->refresh_token;

		$data = [
			'grant_type'	=>	'refresh_token',
			'client_id'		=>	$client_id,
			'client_secret'	=>	$client_secret,
			'refresh_token'	=>	$refreshToken 
		];

		$client = new Client([
			'verify'	=>	false
		]);

		$res = $client->post("{$server}/{$workspace}/oauth2/token", [
			'form_params'	=>	$data
		]);

		if($res->getStatusCode() == 200){
			\Log::debug("Token refreshed successfully: " . $res->getBody());
			$response = json_decode($res->getBody());
			$response->expiry = time() + $response->expires_in;
			Storage::disk('local')->put('pmauthentication.json', json_encode($response));
			\Log::debug("Token saved to pmauthentication.json file");

			setcookie("access_token",  $response->access_token,  $response->expiry);
			setcookie("refresh_token", $response->refresh_token); //refresh token doesn't expire
			setcookie("client_id",     $client_id);
			setcookie("client_secret", $client_secret);
			return $response->access_token;
		}
		else {
			\Log::error("Token was not refreshed successfully. There was an error");
			return false;
		}
	}

	public static function updateCaseVariables($case, $data, $del_index = 1){
		if (App::environment('staging')) {
        	$url = "http://".env('PM_SERVER')."/api/1.0/workflow/cases/{$case}/variable";
        }else{
        	$url = "https://".env('PM_SERVER_DOMAIN')."/api/1.0/workflow/cases/{$case}/variable";
        }

        $authenticationData = json_decode(Storage::get("pmauthentication.json"));

        $response = Self::executeREST($url, "PUT", $data, (new self)->authData()->access_token);

        // dd($response);

        return $response;
	}

	public function authData(){
		$authenticationData = json_decode(Storage::get("pmauthentication.json"));

		return $authenticationData;
	}


	public static function login(){

	}

}