<?php

namespace App\Helpers\HCSU\AdobeSign;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Storage;

class AdobeClient{
	public static function auth($code, $api_access_point, $web_access_point){
		$url = "{$api_access_point}oauth/token?code={$code}&client_id=".env('ADOBE_SIGN_APPLICATION_ID')."&client_secret=".env('ADOBE_SIGN_CLIENT_SECRET')."&redirect_uri=".env('ADOBE_SIGN_REDIRECT_URI')."&grant_type=authorization_code";
		\Log::debug("Trying Adobe Sign Log in: {$url}");
		// dd($url);
		$client = new Client();

		$res = $client->request('POST', $url, [
			'headers'	=>	[
				'Content-Type'	=>	'application/x-www-form-urlencoded'
			]
		]);
		$data = json_decode($res->getBody()->getContents());
		$data->api_access_point = $api_access_point;
		$data->web_access_point = $web_access_point;
		$data->expiry_time = time() + $data->expires_in;
		$data->edatetime = date('Y-m-d H:i:s', $data->expiry_time);
		\Storage::disk('local')->put('adobe-sign.json', json_encode($data));
	}

	public static function uploadDocument($file, $file_name){
		$auth = Self::authdata();
		if (time() > $auth->expiry_time) {
			$auth = self::refreshToken();
		}
		$url = $auth->api_access_point . "api/rest/v6/transientDocuments";
		$client = new Client([
			'headers'	=>	[ 'Authorization'	=>	"Bearer {$auth->access_token}" ]
		]);

		$options = [
			'multipart'	=>	[
				[
					'Content-Type'	=> 'multipart/form-data',
					'name'			=>	'File',
					'contents'		=>	\Storage::get($file),
					'filename'		=>	$file_name . '.pdf'
				]
			]
		];

		$response = $client->post($url, $options);

		return (json_decode($response->getBody()->getContents()))->transientDocumentId;
	}

	public static function uploadLibraryDocument($template_id, $data, $title){
		$auth = Self::authdata();
		$url = $auth->api_access_point . "api/rest/v6/agreements";

		$client = new Client([
			'headers'	=>	[ 
				'Authorization'	=>	"Bearer {$auth->access_token}",
				'Content-Type'	=>	"application/json"
			]
		]);

		$mergeFields = collect($data)->map(function($row, $key) {
			return [
				'defaultValue'	=>	$row,
				'fieldName'		=>	$key
			];
		})->toArray();

		$mergeFields = array_values($mergeFields);

		$options = [
			'json'	=>	[
				'name'				=>	$title,
				'signatureType'		=>	'ESIGN',
				'fileInfos'			=>	[
					[
						'libraryDocumentId '	=>	$template_id
					]
				],
				'state'					=>	"IN_PROCESS",
				'participantSetsInfo'	=>	[
					[
						"memberInfos"	=>	[
							[ "email"	=>	'chrispine.otaalo@un.org' ]
						],
						"order"			=>	1,
						"role"			=>	"SIGNER"
					]
				],
				'mergeFieldInfo'		=>	$mergeFields
			]
		];

		$response = $client->post($url, $options);
		return (json_decode($response->getBody()->getContents()));
	}

	public static function sendDocumentForSigning($documentId, $title){
		$auth = Self::authdata();
		if (time() > $auth->expiry_time) {
			$auth = self::refreshToken();
		}

		$url = $auth->api_access_point . "api/rest/v6/agreements";

		$client = new Client([
			'headers'	=>	[ 
				'Authorization'	=>	"Bearer {$auth->access_token}",
				'Content-Type'	=>	'application/json'
			]
		]);

		$options = [
			'json'	=>	[
				'name'					=>	'Sample Agreement',
				'signatureType'			=>	'ESIGN',
				'fileInfos'				=>	[['transientDocumentId'	=>	$documentId]],
				"formFields"			=>	[
					[
						'name'			=>	'HCSU Manager Signature',
						'inputType'		=>	'SIGNATURE',
						'locations'		=>	[
							'pageNumber'	=>	1,
							"top"			=> 520,
							"left"			=> 162,
							"width"			=>	280,
							"height"		=>	30
						],
						"contentType" => "SIGNATURE",
                "required" => 1,
                "recipientIndex" => 1
					]
				],
				'state'					=>	"IN_PROCESS",
				'participantSetsInfo'	=>	[
					[
						"memberInfos"	=>	[
							[ "email"	=>	'chrispine.otaalo@un.org' ]
						],
						"order"			=>	1,
						"role"			=>	"SIGNER"
					]
				]
			]
		];

		$response = $client->post($url, $options);

		return (json_decode($response->getBody()->getContents()))->id;
	}

	public static function getSigningURLs($agreement_id){
		$auth = Self::authdata();
		if (time() > $auth->expiry_time) {
			$auth = self::refreshToken();
		}

		$url = $auth->api_access_point . "api/rest/v6/agreements/{$agreement_id}/signingUrls";

		$client = new Client([
			'headers'	=>	[ 
				'Authorization'	=>	"Bearer {$auth->access_token}"
			]
		]);

		$response = $client->get($url);

		return json_decode($response->getBody()->getContents());

	}

	public static function getLibraryDocuments(){
		$auth = Self::authdata();

		$url = $auth->api_access_point . "api/rest/v6/libraryDocuments";

		$client = new Client([
			'headers'	=>	[ 
				'Authorization'	=>	"Bearer {$auth->access_token}"
			]
		]);

		$response = $client->get($url);

		return json_decode($response->getBody()->getContents());
	}

	protected static function refreshToken(){
		$auth = self::authdata();

		$url = "{$auth->api_access_point}oauth/token?refresh_token={$auth->refresh_token}&grant_type=refresh_token&client_id=" . env('ADOBE_SIGN_APPLICATION_ID') . "&client_secret=" . env('ADOBE_SIGN_CLIENT_SECRET');

		$client = new Client();

		$res = $client->request('POST', $url, [
			'headers'	=>	[
				'Content-Type'	=>	'application/x-www-form-urlencoded'
			]
		]);

		$data = json_decode($res->getBody()->getContents());
		$data->api_access_point = $api_access_point;
		$data->web_access_point = $web_access_point;
		$data->expiry_time = time() + $data->expires_in;
		$data->edatetime = date('Y-m-d H:i:s', $data->expiry_time);
		\Storage::disk('local')->put('adobe-sign.json', json_encode($data));

		return $data;
		
	}

	protected static function authdata(){
		$authdata = json_decode(\Storage::disk('local')->get('adobe-sign.json'));

		return $authdata;
	}
}