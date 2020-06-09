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
    }
}
