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
	function login(){
		return view('auth.focalpoints.login');
	}

	function resetPassword(Request $request){
		$data['token'] = $request->token;
		return view('auth.focalpoints.resetpassword')->with($data);
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

		$broker = Password::broker();
		// dd($request->all());
		$r = $request->only('email', 'new_password', 'new_password_confirmation', 'token');

		$r['password'] = $r['new_password'];
		$r['password_confirmation'] = $r['new_password_confirmation'];

		unset($r['new_password']);
		unset($r['new_password_confirmation']);

		$res = $broker->reset($r, function($user, $password){
			$user->password = Hash::make($password);
			$user->setRememberToken(Str::random(60));
			$user->save();

			event(new PasswordReset($user));
			Auth::guard()->login($user);
		});

		switch($res){
			case Password::PASSWORD_RESET:
				return \Redirect::route('focalpoints-home')->with('status', 'Successfully Reset Password');
			break;

			default:
				$errors[] = "There was an issue with your request";
			break;
		}

		if ($errors) {
			return \Redirect::back()->withErrors($errors)->withInput();
		}		
	}
}
