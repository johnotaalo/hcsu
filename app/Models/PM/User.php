<?php

namespace App\Models\PM;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = "pm";
	protected $table = "USERS";
	protected $primaryKey = "USR_ID";
	protected $hidden = ["USR_PASSWORD", "USR_ROLE"];
	protected $appends = ["initials"];

	protected function getInitialsAttribute(){
	    $firstName = $this->USR_FIRSTNAME;
	    $lastName = $this->USR_LASTNAME;

	    $initials = "";

	    if (!$lastName){
	        $initials = strtoupper($firstName[0] . $firstName[1]);
        }else{
	        $lastNameFrags = explode(" ", $lastName);
	        $firstNameFrags = explode(" ", $firstName);

	        foreach ($firstNameFrags as $frag){
	            $initials .= strtoupper($frag[0]);
            }

	        foreach ($lastNameFrags as $frag){
                $initials .= strtoupper($frag[0]);
            }
        }
	    return $initials;
    }
}
