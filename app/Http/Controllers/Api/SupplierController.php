<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
	function searchSupplier(Request $request){
		$searchTerm = $request->query('q');
		return \App\Models\Supplier::where("SUPPLIER_NAME", "LIKE", "%{$searchTerm}%")->get();
	}
}
