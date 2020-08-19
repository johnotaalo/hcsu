<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class ClearingAgent extends Model{
	protected $table = "ref_clearing_agents";
    protected $connection = "2019";
    protected $primaryKey = "ID";

    protected $fillable = ['CLEARING_AGENT_NAME', 'CLEARING_AGENT_ADDRESS'];

    public $timestamps = false;
}