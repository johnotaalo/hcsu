<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormTemplate extends Model
{
    protected $fillable = ["form_name", "process", "task", "input_document", "path", "step", "type", "ADOBE_SIGN_TEMPLATE"];

    protected $appends = ['file_url'];

    public function process(){
    	return $this->belongsTo(\App\Models\PM\Process::class, "process", "PRO_UID");
    }

    public function getFileUrlAttribute(){
    	return \Storage::url($this->path);
    }
}
