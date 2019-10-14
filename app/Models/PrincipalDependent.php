<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PrincipalDependent extends Model
{
    protected $primaryKey = "ID";
	protected $table = "PRINCIPAL_DEPENDENT";
  	protected $appends = ["image_link", "relationship", "passports"];

    protected $fillable = ["HOST_COUNTRY_ID", "PRINCIPAL_ID", "INDEX_NO", "LAST_NAME", "OTHER_NAMES", "RELATIONSHIP_ID", "COUNTRY", "EMPLOYMENT_DETAILS", "PASSPORT_NO", "DATE_OF_BIRTH", "IMAGE", "OLD_REF_ID"];

    protected $dates = ['DATE_OF_BIRTH'];

    public function getImageLinkAttribute(){
    	return Storage::url($this->IMAGE);
    }

    public function getRelationshipAttribute(){
    	$relationship = \App\Models\Relationship::where('REL_ID', $this->RELATIONSHIP_ID)->first();
        return $relationship;
    }

    public function getPassportsAttribute(){
        $passports = \App\Models\PrincipalPassport::where('PRINCIPAL_ID', $this->HOST_COUNTRY_ID)->get();
        return $passports;
    }

    public function principal(){
      return $this->belongsTo('\App\Models\Principal', 'PRINCIPAL_ID', 'HOST_COUNTRY_ID');
    }

    public function relationshipX(){
      return $this->belongsTo('\App\Models\Relationship', 'RELATIONSHIP_ID', 'REL_ID');
    }
}
