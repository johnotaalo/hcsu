<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
	function all(){
		return \App\Models\Supplier::all();
	}

	function create(Request $request){
		$validateData = $request->validate([
			'SUPPLIER_NAME'			=>	'required|unique:2019.ref_suppliers',
			'SUPPLIER_ADDRESS'		=>	'required',
			'SUPPLIER_SHORT_NAME'	=>	'required|unique:2019.ref_suppliers',
			'PIN'					=>	'required|unique:2019.ref_suppliers'
		]);

		return \App\Models\Supplier::create($request->input());
	}

	function update(Request $request){
		$validateData = $request->validate([
			'SUPPLIER_NAME'			=>	'required',
			'SUPPLIER_ADDRESS'		=>	'required',
			'SUPPLIER_SHORT_NAME'	=>	'required',
			'PIN'					=>	'required'
		]);

		$updated = \App\Models\Supplier::find($request->ID)->update($request->except('_method', 'ID'));

		return ['updated' => $updated];
	}

	function searchSupplier(Request $request){
		$searchTerm = $request->query('q');
		return \App\Models\Supplier::where("SUPPLIER_NAME", "LIKE", "%{$searchTerm}%")->get();
	}
}
