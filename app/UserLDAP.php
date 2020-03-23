<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Yadahan\AuthenticationLog\AuthenticationLogable;

class UserLDAP extends Authenticatable
{
    use HasApiTokens, Notifiable, AuthenticationLogable;

    protected $primaryKey = 'id';

    protected $table = "users_ldap";

    protected $guard = 'ldap';

    protected $fillable = ['name', 'username', 'email', 'password', 'index_no'];

    protected $hidden = ['password', 'remember_token'];

    public function notifyAuthenticationLogVia()
    {
        return ['mail'];
    }
}
