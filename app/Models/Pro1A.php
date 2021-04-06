<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro1A extends Model
{
    protected $table = "DF_01";
    protected $connection = "pm_data";

    protected $appends = ['clearing_agent', 'port'];

    public function getClearingAgentAttribute(){
    	// return $this->hasOne(\App\Models\Ref\ClearingAgent::class, "CLEARING_AGENT", "ID");
    	$agent = \App\Models\Ref\ClearingAgent::where('ID', $this->CLEARING_AGENT)->first();
    	return $agent;
    }

    public function getPortAttribute(){
    	$port = \App\Models\Ref\PortsOfClearance::where('ID', $this->PORT_OF_CLEARANCE)->first();
    	return $port;
    }

    public function getDataAttribute(){
        return \App\Helpers\HCSU\Data\Pro1AData::get($this->CASE_NO);
    }
}
