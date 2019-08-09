<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    function login(Request $request){
    	$credentials = $request->only('email', 'password');

		if ($token = $this->guard()->attempt($credentials)) {
			return response()->json(['status' => 'success'], 200)->header('Authorization', $token);
		}

		return response()->json(['error' => 'login_error'], 401);
    }

    function user(){
    	$user = User::find(Auth::user()->id);

    	return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    function refresh(){
    	if ($token = $this->guard()->refresh()) {
            return response()
                ->json(['status' => 'successs'], 200)
                ->header('Authorization', $token);
        }

        return response()->json(['error' => 'refresh_token_error'], 401);
    }

    function logout(){
    	$this->guard()->logout();

    	return response()->json([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);
    }


    private function guard(){
        return Auth::guard();
    }
}