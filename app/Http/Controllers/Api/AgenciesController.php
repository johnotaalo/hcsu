<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Agency;

class AgenciesController extends Controller
{
    function searchAgencies(Request $request){
    	$searchTerm = $request->query('q');
    	return Agency::where('ACRONYM', 'LIKE', "%{$searchTerm}%")->orWhere('AGENCYNAME', 'LIKE', '%{$searchTerm}%')->get();
    }
}
