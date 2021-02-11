<?php

namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Tymon\JWTAuth\Contracts\JWTSubject;

use App\Enums\UserType;
use Yadahan\AuthenticationLog\AuthenticationLogable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, AuthenticationLogable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'user_type', 'ext_id', 'index_no', 'objectguid'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['focal_point', 'type', 'principal'];

    protected $connection = 'mysql';

    // public function getJWTIdentifier()
    // {
    //     return $this->getKey();
    // }

    // public function getJWTCustomClaims()
    // {
    //     return [];
    // }

    public function getFocalPointAttribute(){
        if ($this->user_type == UserType::getInstance(UserType::FocalPoint) && $this->ext_id != null ) {
            return \App\AgencyFocalPoint::with('agency')->find($this->ext_id);
        }
    }

    public function getPrincipalAttribute(){
        return \App\Models\Principal::where("USER_ID", $this->id)->first();
    }

    public function notifyAuthenticationLogVia()
    {
        return ['mail'];
    }

    public function getTypeAttribute(){
        if (is_null($this->user_type) || $this->user_type == "") {
            return "Client";
        }

        return (UserType::fromValue((int) $this->user_type))->description;
    }

    public function findForPassport($username)
    {
        dd('username');
        session(['username' => $username]);
        return $this->where('username', $username)->first();
    }

    public function validateForPassportPasswordGrant($password)
    {
        $credentials = ['username' => session()->pull('username'), 'password' => $password];
        return Auth::attempt($credentials);
    }
}
