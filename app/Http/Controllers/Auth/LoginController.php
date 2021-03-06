<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Adldap\Laravel\Facades\Adldap;
use Illuminate\Support\Facades\Auth;

ini_set('max_execution_time', 0);

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    protected $username;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:ldap')->except('logout');

        $this->username = $this->findUsername();
    }

    public function findUsername()
    {
        $login = request()->input('email');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    protected function attemptLogin(Request $request){
//         try {
//             Adldap::connect();
//         } catch (\Exception $e) {
//              \Log::error("AD is not connecting");
//              dd($e->getMessage());
//         }
        // if (request()->input('location') == "client-portal") {
//        dd(\Config::get('settings.ldap_enabled'));
            $credentials = request()->only($this->username, 'password');
            Auth::guard('web')->attempt($credentials, true);
        // }else{
        //     Auth::attempt(['email' => request($this->username), 'password' => request('password')]);
        // }
    }
}
