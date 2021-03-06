<?php

namespace App\Helpers\HCSU\AdobeSign;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use phpDocumentor\Reflection\Types\Array_;
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
		$url = $auth->api_access_point . "api/rest/v5/transientDocuments";
		$client = new Client([
			'headers'	=>	[ 
				'Authorization'	=>	"Bearer {$auth->access_token}",
				'x-api-user'	=>	"email:" . env("ADOBE_SIGN_API_USER") 
			]
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

	public static function uploadMultiSignatureLibraryDocument($template_id, $data, $title, $clientEmail, $nv = null, $nvOnly = false){
		$auth = Self::authdata();

		if (time() > $auth->expiry_time) {
			$auth = self::refreshToken();
		}

		$url = $auth->api_access_point . "api/rest/v5/agreements";

		$client = new Client([
			'headers'	=>	[
				'Authorization'	=>	"Bearer {$auth->access_token}",
				'Content-Type'	=>	"application/json",
				'x-api-user'	=>	"email:" . env("ADOBE_SIGN_API_USER")
			]
		]);

		$mergeFields = collect($data)->map(function($row, $key) {
			return [
				'defaultValue'	=>	$row,
				'fieldName'		=>	$key
			];
		})->toArray();

		$mergeFields = array_values($mergeFields);
		$files = [];
		if(!$nvOnly){
			$files = [['libraryDocumentId'	=>	$template_id]];
		}

		if($nv){
			$files[] = ['libraryDocumentId'	=>	$nv];
		}

		$options = [
			'json'	=>	[
				'documentCreationInfo'	=>	[
					'recipientSetInfos'	=>	[
						[
							"recipientSetRole"			=>	"SIGNER",
							"recipientSetMemberInfos"	=>	[[ "email" => $clientEmail ]]
						],
						[
							"recipientSetRole"			=>	"SIGNER",
							"recipientSetMemberInfos"	=>	Self::getSignatories()
						]
					],
					"signatureType"		=>	"ESIGN",
					"signatureFlow"		=>	"SENDER_SIGNATURE_NOT_REQUIRED",
					"name"				=>	$title,
					"fileInfos"			=>	$files,
					"mergeFieldInfo"	=>	$mergeFields
				]
			]
		];

		\Log::debug("options: " . json_encode($options['json']));

		$response = $client->post($url, $options);
		// \Log::error("Response: " . $response->getBody()->getContents());
		return (json_decode($response->getBody()->getContents()))->agreementId;
	}

	public static function uploadLibraryDocument($template_id, $data, $title, $nv = null, $nvOnly = false){
		$auth = Self::authdata();

		if (time() > $auth->expiry_time) {
			$auth = self::refreshToken();
		}

		$url = $auth->api_access_point . "api/rest/v5/agreements";

		$client = new Client([
			'headers'	=>	[
				'Authorization'	=>	"Bearer {$auth->access_token}",
				'Content-Type'	=>	"application/json",
				'x-api-user'	=>	"email:" . env("ADOBE_SIGN_API_USER")
			]
		]);

		$mergeFields = collect($data)->map(function($row, $key) {
			return [
				'defaultValue'	=>	$row,
				'fieldName'		=>	$key
			];
		})->toArray();

		$mergeFields = array_values($mergeFields);
		$files = [];

		if($nv){
			$files[] = ['libraryDocumentId'	=>	$nv];
		}

		if(!$nvOnly){
			$files[] = ['libraryDocumentId'	=>	$template_id];
		}

		\Log::debug("Documents: " . json_encode($files));

		$options = [
			'json'	=>	[
				'documentCreationInfo'	=>	[
					'recipientSetInfos'	=>	[
						[
							"recipientSetRole"			=>	"SIGNER",
							"recipientSetMemberInfos"	=>	Self::getSignatories()
						]
					],
					"signatureType"		=>	"ESIGN",
					"signatureFlow"		=>	"SENDER_SIGNATURE_NOT_REQUIRED",
					"name"				=>	$title,
					"fileInfos"			=>	$files,
					"mergeFieldInfo"	=>	$mergeFields
				]
			]
		];

		\Log::debug("options: " . json_encode($options['json']));

		$response = $client->post($url, $options);
		// \Log::error("Response: " . $response->getBody()->getContents());
		return (json_decode($response->getBody()->getContents()))->agreementId;
	}

	public static function sendGenericDocument($template_id, $data, $title, $emails){
        $auth = Self::authdata();

        if (time() > $auth->expiry_time) {
            $auth = self::refreshToken();
        }

        $url = $auth->api_access_point . "api/rest/v5/agreements";

        $client = new Client([
            'headers'	=>	[
                'Authorization'	=>	"Bearer {$auth->access_token}",
                'Content-Type'	=>	"application/json",
				'x-api-user'	=>	"email:" . env("ADOBE_SIGN_API_USER")
            ]
        ]);

        $mergeFields = collect($data)->map(function($row, $key) {
            return [
                'defaultValue'	=>	$row,
                'fieldName'		=>	$key
            ];
        })->toArray();

        $mergeFields = array_values($mergeFields);
        $files = ['libraryDocumentId'	=>	$template_id];

        \Log::debug("Emails: " . json_encode($emails));
        \Log::debug("Documents: " . json_encode($files));

        $options = [
            'json'	=>	[
                'documentCreationInfo'	=>	[
                    'recipientSetInfos'	=>	[
                        [
                            "recipientSetRole"			=>	"SIGNER",
                            "recipientSetMemberInfos"	=>	$emails
                        ]
                    ],
                    "signatureType"		=>	"ESIGN",
                    "signatureFlow"		=>	"SENDER_SIGNATURE_NOT_REQUIRED",
                    "name"				=>	$title,
                    "fileInfos"			=>	$files,
                    "mergeFieldInfo"	=>	$mergeFields
                ]
            ]
        ];

        \Log::debug("options: " . json_encode($options['json']));

        $response = $client->post($url, $options);
        // \Log::error("Response: " . $response->getBody()->getContents());
        return (json_decode($response->getBody()->getContents()))->agreementId;
    }

    public static function sendReminder($agreementId){
        $auth = Self::authdata();
        if (time() > $auth->expiry_time) {
            $auth = self::refreshToken();
        }

        $url = $auth->api_access_point . "api/rest/v5/reminders";

        $client = new Client([
            'headers'	=>	[
                'Authorization'	=>	"Bearer {$auth->access_token}"
            ]
        ]);

        $options = [
            'json'  =>  [
//                'reminderCreationInfo'  =>  [
                    'agreementId'       =>  $agreementId
//                ]
            ]
        ];

        $response = $client->post($url, $options);

        return json_decode($response->getBody()->getContents());
    }

	public static function getSignatories(){
		$signatories = \App\Models\AdobeSignSignatory::where('status', 1)->get();
		$signatoriesData = [];
		if ($signatories) {
			foreach ($signatories as $signatory) {
				$signatoriesData[] = [
					"email"		=>	$signatory->email
				];
			}
		}

		return $signatoriesData;
	}

	public static function mofaTest($case){
		$auth = Self::authdata();

		if (time() > $auth->expiry_time) {
			$auth = self::refreshToken();
		}

		$url = $auth->api_access_point . "api/rest/v5/agreements";

		$client = new Client([
			'headers'	=>	[
				'Authorization'	=>	"Bearer {$auth->access_token}",
				'Content-Type'	=>	"application/json"
			]
		]);

		$data = [
			'serial_no' => "VAT/{$case}"
		];

		$template_id = "3AAABLblqZhA5xU4JOB7lPrskJ4j0Tnmxdhhwfm9UWQsI_fKTdFLHQBtb0zamMUSW--APb0OCb0rykfPxwWltWeSVlNc3dOt5";

		$options = [
			'json'	=>	[
				'documentCreationInfo'	=>	[
					'recipientSetInfos'	=>	[
						[
							"recipientSetRole"			=>	"SIGNER",
							"recipientSetMemberInfos"	=>	[
								["email"	=>	"chrispine.otaalo@un.org"]
							]
						],
						[
							"recipientSetRole"			=>	"SIGNER",
							"recipientSetMemberInfos"	=>	[
								["email"	=>	"chrispine.otaalo@gmail.com"]
							]
						],
						[
							"recipientSetRole"			=>	"SIGNER",
							"recipientSetMemberInfos"	=>	[
								["email"	=>	"c.otaalo@gmail.com"]
							]
						]
					],
					"signatureType"		=>	"ESIGN",
					"signatureFlow"		=>	"SENDER_SIGNATURE_NOT_REQUIRED",
					"name"				=>	"Sample Document",
					"fileInfos"			=>	[['libraryDocumentId'	=>	$template_id]],
					"mergeFieldInfo"	=>	$data
				]
			]
		];

		$response = $client->post($url, $options);
		return (json_decode($response->getBody()->getContents()))->agreementId;
	}

	public static function sendDocumentForSigning($documentId, $title){
		$auth = Self::authdata();
		if (time() > $auth->expiry_time) {
			$auth = self::refreshToken();
		}

		$url = $auth->api_access_point . "api/rest/v5/agreements";

		$client = new Client([
			'headers'	=>	[
				'Authorization'	=>	"Bearer {$auth->access_token}",
				'Content-Type'	=>	'application/json'
			]
		]);

		$options = [
			'json'	=>	[
				'documentCreationInfo'	=>	[
					'recipientSetInfos'	=>	[
						[
							"recipientSetRole"			=>	"SIGNER",
							"recipientSetMemberInfos"	=>	[
								["email"	=>	"chrispine.otaalo@un.org"]
							]
						]
					],
					"signatureType"		=>	"ESIGN",
					"signatureFlow"		=>	"SENDER_SIGNATURE_NOT_REQUIRED",
					"name"				=>	$title,
					"fileInfos"			=>	[['transientDocumentId'	=>	$documentId]],
					"formFields"		=>	[
						[
							"name"			=>	"Manager Signature",
							"inputType"		=>	"SIGNATURE",
							"locations"		=>	[
								"pageNumber"	=>	1,
								"top"			=>	520,
								"left"			=>	162
							],
							"contentType" => "SIGNATURE",
							"required" => true,
							"recipientIndex" => 1
						]
					]
				]
			]
		];

		die(json_encode($options));

		$response = $client->post($url, $options);
		dd(json_decode($response->getBody()->getContents()));

		return (json_decode($response->getBody()->getContents()))->agreementId;
	}

	public static function addStampandSignatureFields($agreementId){
		$auth = Self::authdata();
		if (time() > $auth->expiry_time) {
			$auth = self::refreshToken();
		}

		$url = $auth->api_access_point . "api/rest/v5/agreements/{$agreementId}/formFields";

		$client = new Client([
			'headers'	=>	[
				'Authorization'	=>	"Bearer {$auth->access_token}",
				'Content-Type'	=>	'application/json'
			]
		]);

		$options = [
			'json'	=>	[
				"fields"	=>	[
					"locations"	=>	[
						[
							"height"		=>	36,
							"left"			=>	75,
							"pageNumber"	=>	"1",
							"top"			=>	200,
							"width"			=>	150
						]
					],
					"contentType" => "SIGNATURE",
					"name"=> "sigBlock1",
					"inputType"=> "SIGNATURE",
					"recipientIndex"=>1
				]
			]
		];

		$response = $client->put($url, $options);

		return (json_decode($response->getBody()->getContents()));
	}

	public static function getSigningURLs($agreement_id){
		$auth = Self::authdata();
		if (time() > $auth->expiry_time) {
			$auth = self::refreshToken();
		}

		$url = $auth->api_access_point . "api/rest/v5/agreements/{$agreement_id}/signingUrls";

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

		if (time() > $auth->expiry_time) {
			$auth = self::refreshToken();
		}

		$url = $auth->api_access_point . "api/rest/v5/libraryDocuments";

		$client = new Client([
			'headers'	=>	[
				'Authorization'	=>	"Bearer {$auth->access_token}"
			]
		]);

		$response = $client->get($url);

		// \Log::debug("Library Documents Reponse: " . $response->getBody()->getContents());

		return json_decode($response->getBody()->getContents());
	}

	protected static function refreshToken(){
		$auth = self::authdata();

		\Log::debug("Refreshing token...");

		$url = "{$auth->api_access_point}oauth/refresh?refresh_token={$auth->refresh_token}&grant_type=refresh_token&client_id=" . env('ADOBE_SIGN_APPLICATION_ID') . "&client_secret=" . env('ADOBE_SIGN_CLIENT_SECRET');

		\Log::debug("Auth url: {$url}");

		$client = new Client();

		$res = $client->request('POST', $url, [
			'headers'	=>	[
				'Content-Type'	=>	'application/x-www-form-urlencoded'
			]
		]);

		$data = json_decode($res->getBody()->getContents());

		$data->api_access_point = $auth->api_access_point;
		$data->web_access_point = $auth->web_access_point;
		$data->expiry_time = time() + $data->expires_in;
		$data->edatetime = date('Y-m-d H:i:s', $data->expiry_time);
		$data->refresh_token = $auth->refresh_token;

		\Storage::disk('local')->put('adobe-sign.json', json_encode($data));

		return $data;

	}

	protected static function authdata(){
		$authdata = json_decode(\Storage::disk('local')->get('adobe-sign.json'));

		return $authdata;
	}

	public static function getAgreementDetails($agreementId){
		$auth = Self::authdata();
		if (time() > $auth->expiry_time) {
			$auth = self::refreshToken();
		}

		$url = $auth->api_access_point . "api/rest/v5/agreements/{$agreementId}";

		$client = new Client([
			'headers'	=>	[
				'Authorization'	=>	"Bearer {$auth->access_token}"
			]
		]);

		$response = $client->get($url);

		return json_decode($response->getBody()->getContents());
	}

	public static function downloadSignedDocument($agreementId){
		$auth = Self::authdata();
		if (time() > $auth->expiry_time) {
			$auth = self::refreshToken();
		}

		$url = $auth->api_access_point . "api/rest/v5/agreements/{$agreementId}/combinedDocument?auditReport=true";

		$client = new Client([
			'headers'	=>	[
				'Authorization'	=>	"Bearer {$auth->access_token}"
			]
		]);

		$response = $client->get($url);

		return $response->getBody()->getContents();
	}
}
