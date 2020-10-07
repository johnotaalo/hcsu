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
    	$searchQueries = $request->get('normalSearch');
		$limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $queryBuilder = \App\Models\Ref\ClearingAgent::select("ID", "CLEARING_AGENT_NAME", "CLEARING_AGENT_ADDRESS");

        if($searchQueries){
        	$queryBuilder->where('CLEARING_AGENT_NAME', 'LIKE', "%{$searchQueries}%")
        					->orWhere('CLEARING_AGENT_ADDRESS', 'LIKE', "%{$searchQueries}%");
        }

        if ($orderBy) {
        	$queryBuilder->orderBy($orderBy, ($ascending == 1) ? 'ASC' : 'DESC');
        }

        $count = $queryBuilder->count();

        $queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));
        $agents = $queryBuilder->get();

    	return [
    		'data'	=>	$agents,
    		'count'	=>	$count
    	];
    }
}
