<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlanketVATBatch extends Model
{
    protected $connection = "pm_data";
    protected $table = "VAT_02_BATCHES";

    protected $fillable = ['batch_date', 'comment', 'is_active'];

    public function cases(){
    	return $this->hasMany(\App\Models\BlanketVAT::class, "BATCH_ID");
    }
}
