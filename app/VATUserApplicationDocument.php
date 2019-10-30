<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VATUserApplicationDocument extends Model
{
    protected $connection = "pm_data";
    protected $table = "VAT_01_USER_APPLICATION_DOCUMENTS";
    protected $fillable = ["APPLICATION_ID", "PATH", "DOCUMENT_UID"];
    protected $appends = array('document_link');

    function getDocumentLinkAttribute(){
    	return Storage::url(str_replace( '/var/www/html/hcsu/storage/', '', $this->PATH ));
    }
}
