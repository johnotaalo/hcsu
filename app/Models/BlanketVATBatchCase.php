<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlanketVATBatchCase extends Model
{
    protected $connection = "pm_data";
    protected $table = "VAT_02_BATCH_CASES";

    protected $fillable = ['batch_id', 'case_no'];
}
