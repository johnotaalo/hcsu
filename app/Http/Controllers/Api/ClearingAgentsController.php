<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClearingAgentsController extends Controller
{
	function create(Request $request){
		$validated = $request->validate([
			'CLEARING_AGENT_NAME'		=>	'required|unique:2019.ref_clearing_agents',
			'CLEARING_AGENT_ADDRESS'	=>	'required'
		]);

		return \App\Models\Ref\ClearingAgent::create($request->input());
	}

	function update(Request $request){
		$validated = $request->validate([
			'CLEARING_AGENT_NAME'		=>	'required',
			'CLEARING_AGENT_ADDRESS'	=>	'required'
		]);

		$updated = \App\Models\Ref\ClearingAgent::find($request->ID)->update($request->except('_method', 'ID'));

		return ['updated' => $updated];
	}

    function all(Request $request){
    	return \App\Models\Ref\ClearingAgent::orderBy('CLEARING_AGENT_NAME', 'ASC')->get();
    }
}
