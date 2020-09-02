<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
	function all(Request $request){
		$searchQueries = $request->get('normalSearch');
		$limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $queryBuilder = \App\Models\Supplier::select('SUPPLIER_NAME', 'SUPPLIER_ADDRESS', 'SUPPLIER_SHORT_NAME', 'PIN');

        if($searchQueries){
        	$queryBuilder->where('SUPPLIER_NAME', 'LIKE', "%{$searchQueries}%")
        					->orWhere('SUPPLIER_ADDRESS', 'LIKE', "%{$searchQueries}%")
        					->orWhere('SUPPLIER_SHORT_NAME', 'LIKE', "%{$searchQueries}%")
        					->orWhere('PIN', 'LIKE', "%{$searchQueries}%");
        }

        if ($orderBy) {
        	$queryBuilder->orderBy($orderBy, ($ascending == 1) ? 'ASC' : 'DESC');
        }

        $count = $queryBuilder->count();

        $queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));
        $suppliers = $queryBuilder->get();

		return [
			'data'		=>	$suppliers,
			'count'		=>	$count
		];
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
