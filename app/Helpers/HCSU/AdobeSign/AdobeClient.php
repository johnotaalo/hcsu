<?php

namespace App\Helpers\HCSU\AdobeSign;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Storage;

class AdobeClient{
	public static function auth($code, $api_access_point){
		$url = "{$api_access_point}oauth/token?code={$code}&client_id=".env('ADOBE_SIGN_APPLICATION_ID')."&client_secret=".env('ADOBE_SIGN_CLIENT_SECRET')."&redirect_uri=".env('ADOBE_SIGN_REDIRECT_URI')."&grant_type=authorization_code";
		\Log::debug("Trying Adobe Sign Log in: {$url}");
		// dd($url);
		$client = new Client();

		$res = $client->request('POST', $url, [
			'headers'	=>	[
				'Content-Type'	=>	'application/x-www-form-urlencoded'
			]
		]);
		\Storage::disk('local')->put('adobe-sign.json', $res->getBody()->getContents());

		/*, [
			'client_id'			=>	env('ADOBE_SIGN_APPLICATION_ID'),
			'redirect_uri'		=>	'/api/adobe-sign/callback',
			'scope'				=>	'*',
			'response_type'		=>	'code'
		]*/
		// dd($res->getBody());
	}
}