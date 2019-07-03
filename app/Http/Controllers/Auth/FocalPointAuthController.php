<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AgencyFocalPoint;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class FocalPointAuthController extends Controller
{

	function resetPassword(Request $request){
		return view('auth.focalpoints.resetpassword');
	}

	function changePassword(Request $request){
		$validatedData = $request->validate([
			'email'					=>	'required',
			'new_password'			=>	'required|same:new_password_confirmation'
		]);

		$email = $request->input('email');
		$new_password = $request->input('new_password');
		$password_confirmation = $request->input('new_password_confirmation');

		$errors = [];

		$broker = Password::broker('focalPoints');
		dd($broker);
		$r = $request->only('email', 'new_password', 'new_password_confirmation', 'token');
		

		$broker->reset($r, function($user, $password){
			echo "user";die;
			dd($user);
		});
		// dd($broker);
		if ($errors) {
			return \Redirect::back()->withErrors($errors)->withInput();
		}		
	}
}
