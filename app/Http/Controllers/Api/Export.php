<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Export extends Controller
{
    function exportVAT(Request $request){
    	$vat = \App\Models\VAT::all();

    	$vatList = $vat->map(function($item){
    		$vatData = \App\Helpers\HCSU\Data\VATData::get($item->CASE_NO);
    		// dd($vatData);
    		return [
    			'CASE_NO'		=>	$item->CASE_NO,
    			'AGENCY'		=>	$vatData->client->organization,
    			'PIN_NO'		=>	$vatData->vat->supplierVAT,
    			'SUPPLIER'		=>	$vatData->vat->supplierName,
    			'DESCRIPTION'	=>	$vatData->vat->goodsDescription,
    			'PFNO'			=>	$vatData->vat->pfNo,
    			'PFDATE'		=>	$vatData->vat->pfDate,
    			'AMOUNT'		=>	$vatData->vat->vatAmount
    		];
    	});

    	$exportData = new \App\Exports\VATExport($vatList);
    	return \Excel::download($exportData, 'VAT LIST.xlsx');
    }
}
