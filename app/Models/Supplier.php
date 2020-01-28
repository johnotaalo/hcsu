<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $connection = "2019";
    protected $table = "ref_suppliers";

    protected $fillable = ['SUPPLIER_NAME', 'SUPPLIER_ADDRESS', 'SUPPLIER_SHORT_NAME', 'PIN'];
    protected $primaryKey = "ID";

    public $timestamps = false;
}
