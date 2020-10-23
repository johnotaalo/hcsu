<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdobeSignDocuments extends Model
{
    protected $table = "ADOBE_SIGN_DOCUMENTS";

    protected $fillable = ["DOCUMENT_ID", "AGREEMENT_ID", "AGREEMENT_STATUS", "PAYLOAD", "URLS"];

    protected $appends = ["name"];

    public function getNameAttribute(){
    	$payload = $this->PAYLOAD;

    	if ($payload) {
    		$payloadObj = json_decode($payload);

    		return $payloadObj->name;
    	}

    	return null;
    }
}
