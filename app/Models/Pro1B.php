<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro1B extends Model
{
    protected $connection = "pm_data";

    protected $table = "DF_02";

    protected $appends = ['clearing_agent', 'port'];

    public function getClearingAgentAttribute(){
    	// return $this->hasOne(\App\Models\Ref\ClearingAgent::class, "CLEARING_AGENT", "ID");
    	$agent = \App\Models\Ref\ClearingAgent::where('ID', $this->attributes['CLEARING_AGENT'])->first();
    	return $agent;
    }

    public function getPortAttribute(){
    	$port = \App\Models\Ref\PortsOfClearance::where('ID', $this->attributes['PORT_OF_CLEARANCE'])->first();
    	return $port;
    }
}
