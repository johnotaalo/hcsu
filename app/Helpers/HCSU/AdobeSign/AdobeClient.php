<?php

namespace App\Helpers\HCSU\AdobeSign;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Storage;

class AdobeClient{
	public static function auth(){
		// $provider = new KevinEm\OAuth2\Client\AdobeSign([
		// 	'clientId'          => env('ADOBE_SIGN_APPLICATION_ID'),
		// 	'clientSecret'      => env('ADOBE_SIGN_CLIENT_SECRET'),
		// 	'redirectUri'       => '/api/',
		// 	'scope'             => [
		// 		'scope1:type',
		// 		'scope2:type'
		// 	]
		// ]);

		// $adobeSign = new AdobeSign($provider);

		$url = "https://ims-na1.adobelogin.com/ims/authorize?client_id=" . env('ADOBE_SIGN_APPLICATION_ID') . "&redirect_uri=https://hcsudev.unon.org/adobe-sign/callback&scope=openid&response_type=code";
		$client = new Client();

		$res = $client->request('GET', $url);

		/*, [
			'client_id'			=>	env('ADOBE_SIGN_APPLICATION_ID'),
			'redirect_uri'		=>	'/api/adobe-sign/callback',
			'scope'				=>	'*',
			'response_type'		=>	'code'
		]*/
		dd($res->getBody());
	}
}