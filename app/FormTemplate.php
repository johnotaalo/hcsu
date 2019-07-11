<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormTemplate extends Model
{
    protected $fillable = ["form_name", "process", "task", "input_document", "path"];
}
