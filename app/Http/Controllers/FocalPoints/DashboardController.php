<?php

namespace App\Http\Controllers\FocalPoints;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    function index(){
    	if (\Auth::user()->type == "Client" && !\Auth::user()->principal) {
    		$contracts = \App\Models\PrincipalContract::where('INDEX_NO', \Auth::user()->index_no)->get();
    		if (count($contracts) > 0) {
    			if (count($contracts) == 1) {
    				$contract = $contracts->first();
    				$principal = $contract->principal;

    				return redirect()->route('clients-confirm', ['id' => $principal->ID]);
    			}
    		}

    		die("No contracts found");
    	}
    	return view('focalpoint.app');
    }

    function confirmPrincipal(Request $request){
    	$principal = \App\Models\Principal::find($request->id);

    	if ($principal->USER_ID) {
    		return redirect()->route('clients-home');
    	}

    	$data = [
    		'principal'	=>	$principal
    	];

    	return view('focalpoint.client.confirm')->with($data);
    }

    function confirmUser(Request $request){
    	$principal = \App\Models\Principal::findOrFail($request->principal_id);

    	$principal->USER_ID = \Auth::user()->id;

    	$principal->save();

    	return redirect()->route('clients-home');
    }

    function confirmationDetails(Request $request){
    	$agencies = \App\Models\Agency::select('AGENCY_ID', 'ACRONYM')->where('IS_ACTIVE', 1)->orderBy('ACRONYM', 'ASC')->pluck('ACRONYM', 'AGENCY_ID')->toArray();
    	$dispute = \App\Models\UserDispute::where('user_id', \Auth::user()->id)->where('merged', false)->first();
    	return view('focalpoint.client.dispute', ['agencies'=>$agencies, 'dispute'	=>	$dispute]);
    }

    function storeDispute(Request $request){
    	$validatedData = $request->validate([
    		'lastname'		=>	'required',
    		'othernames'	=>	'required',
    		'email'			=>	'required',
    		'agency'		=>	'required',
    		'index_no'		=>	'required'
    	]);

    	$validatedData['user_id']	=	\Auth::user()->id;

    	$dispute = \App\Models\UserDispute::create($validatedData);

    	return redirect()->back();
    }
}
