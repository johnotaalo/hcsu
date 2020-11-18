<?php

namespace App\Http\Controllers\FocalPoints;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplicationsController extends Controller
{
    function get(Request $request){
    	return [
    		'count'	=>	0,
    		'data'	=>	[]
    	];
    }

    function new(Request $request){
    	dd($request->input());
    }
}
