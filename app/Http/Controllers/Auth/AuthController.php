<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public $successStatus = 200;
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('hcsu')-> accessToken; 
            return response()->json(['success' => $success], $this-> successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }

    public function details(Request $request) 
    {
      if(isset($request->type)){
        $user_ip = $request->getClientIp();
        $user_ip = ($user_ip == "10.0.2.2" || $user_ip == "127.0.0.1") ? "10.98.111.148" : $user_ip;

        $log = \App\Models\PM\LoginLog::with('user')->where(['USR_UID' => $request->user])->orderBy('LOG_ID', 'DESC')->first();
        if(!is_null($log) && $log->LOG_STATUS == "ACTIVE"){
          return response()->json($log->user, $this->successStatus);
        }
        return response()->json(['error'=>'Unauthorised'], 401);
      }
      $user = Auth::user();
      return response()->json($user, $this-> successStatus); 
    }
  //   function login(Request $request){
  //   	$credentials = $request->only('email', 'password');

		// if ($token = $this->guard()->attempt($credentials)) {
		// 	return response()->json(['status' => 'success'], 200)->header('Authorization', $token);
		// }

		// return response()->json(['error' => 'login_error'], 401);
  //   }

  //   function user(){
  //   	$user = User::find(Auth::user()->id);

  //   	return response()->json([
  //           'status' => 'success',
  //           'data' => $user
  //       ]);
  //   }

  //   function refresh(){
  //   	if ($token = $this->guard()->refresh()) {
  //           return response()
  //               ->json(['status' => 'successs'], 200)
  //               ->header('Authorization', $token);
  //       }

  //       return response()->json(['error' => 'refresh_token_error'], 401);
  //   }

  //   function logout(){
  //   	$this->guard()->logout();

  //   	return response()->json([
  //           'status' => 'success',
  //           'msg' => 'Logged out Successfully.'
  //       ], 200);
  //   }


  //   private function guard(){
  //       return Auth::guard();
    // }


}
