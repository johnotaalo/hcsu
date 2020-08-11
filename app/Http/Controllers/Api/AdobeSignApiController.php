<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdobeSignApiController extends Controller
{
	function test(){
		$auth = \App\Helpers\HCSU\AdobeSign\AdobeClient::auth();
	}

    function callback(Request $request){
    	\Log::debug("Callback successfully pulled in...");
    	$code = $request->query('code');
    	$api_url = $request->query('api_access_point');
    	$web_url = $request->query('web_access_point');

    	$auth = \App\Helpers\HCSU\AdobeSign\AdobeClient::auth($code, $api_url);
    }

    function authCallback(Request $request){
    	\Log::debug("Auth callback called");
    }
}
