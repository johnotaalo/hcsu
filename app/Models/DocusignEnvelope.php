<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocusignEnvelope extends Model
{
    protected $connection = "pm_data";
    protected $table = "pm_docusign_envelope";
    protected $fillable = ["CASE_NUMBER", "ENVELOPE_ID", "TASK", "PROCESS", "STATUS", "LAST_MODIFIED"];
}
