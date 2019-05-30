<?php

namespace App\Helpers\HCSU;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Storage;

class ProcessMaker {
	public static function executeREST($url, $method="GET", $data = [], $accessToken = ''){
		$authenticationData = json_decode(Storage::get("pmauthentication.json"));
		$tokenExpiry = $authenticationData->expiry;
		\Log::debug("Checking token expiry...");
		if($tokenExpiry < time()){
			\Log::debug("Token has expired. Getting another token");
			$accessToken = (new self)->refreshToken();
			if (!$accessToken) {
				\Log::error("Could not refresh token");
				return ['error' => 'There was an error'];	
			}
		}else{
			\Log::debug("Token is okay. Proceeding...");
		}
		
		$params = [];

		$client = new Client();

		if($accessToken != ''){
			$params['headers'] = [
				'Authorization'	=>	'Bearer ' . $accessToken,
				'Accept'		=>	'application/json'
			];
		}

		$res = $client->request($method, $url, $params);

		if ($res->getStatusCode() == 200) {
			return json_decode($res->getBody());
		}else{
			return ['error' => 'There was an error'];
		}
	}

	private function refreshToken(){
		$authenticationData = json_decode(Storage::get("pmauthentication.json"));
		$server = env("PM_SERVER");
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

		$client = new Client();

		$res = $client->post("{$server}/{$workspace}/oauth2/token", [
			'form_params'	=>	$data
		]);

		if($res->getStatusCode() == 200){
			\Log::debug("Token refreshed successfully");
			$response = json_decode($res->getBody());
			$response->expiry = time() + $response->expires_in;
			Storage::disk('local')->put('pmauthentication.json', json_encode($response));
			\Log::debug("Token saved to pmauthentication.json file");
			return $response->access_token;
		}
		else {
			\Log::error("Token was not refreshed successfully. There was an error");
			return false;
		}
	}
}