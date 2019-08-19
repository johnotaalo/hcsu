<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VATController extends Controller
{
    function addBlanketBatch(Request $request){
    	// dd($request->input());
    	$validatedData = $request->validate([
    		'batch_date'	=>	'required|unique:pm_data.vat_02_batches'
    	]);

    	$res = \App\Models\BlanketVATBatch::create([
    		'batch_date'	=>	new \Carbon\Carbon($request->input('batch_date')),
    		'comment'		=>	$request->input('comment')
    	]);

    	return \App\Models\BlanketVATBatch::with('cases')->find($res->id);
    }

    function getBlanketBatches(Request $request){
    	return \App\Models\BlanketVATBatch::with('cases')->orderBy('batch_date', 'DESC')->get();
    }

    function deleteBlanketBatch(Request $request){
    	// dd($request->input());
    	$batch = \App\Models\BlanketVATBatch::find($request->id);
    	if (count($batch->cases) == 0) {
    		$batch->delete();
    		return ['message' => 'Successfully deleted batch'];
    	}

    	return \Response::json([ 'message' => 'Cannot delete batch because some cases are tied to it!' ], 500);

    	// return response()
    	// 			->json();
    }
}
