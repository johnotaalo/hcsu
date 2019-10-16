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

        // dd($vatList);

    	$exportData = new \App\Exports\VATExport($vatList);
    	return \Excel::download($exportData, 'VAT LIST.xlsx');
    }

    function downloadBlanketVATList(Request $request){
        $batch = \App\Models\BlanketVATBatch::find($request->batch);
        $vat = \App\Models\BlanketVAT::where('BATCH_ID', $request->batch)->get();

        $list = $vat->map(function($item, $key){
            $data = \App\Helpers\HCSU\Data\BlanketVATData::get($item->CASE_NO);
            // dd($data->vatObj->ipmis_log->CREATED_AT);

            return [
                'NO'                    =>  $key + 1,
                'ORGANIZAITON'          =>  $data->client->organization,
                'CASE_NO'               =>  $item->CASE_NO,
                'DATE_APPLIED_AT_MFA'   =>  \Carbon\Carbon::parse($data->vatObj->ipmis_log->CREATED_AT)->format('d/m/Y'),
                'DATE_APPLIED_AT_KRA'   =>  '',
                'DATE_APPROVED'         =>  '',
                'PIN'                   =>  $data->vatObj->supplier->PIN,
                'SUPPLIER_NAME'         =>  $data->vatObj->supplier->NAME,
                'ACCOUNT NO'            =>  $data->vatObj->ACCOUNT_NO,
            ];
        });

        $exportData = new \App\Exports\BlanketVATExport($list, $batch);
        return \Excel::download($exportData, 'Blanket VAT List for Batch: ' . \Carbon\Carbon::parse($batch->batch_date)->format('d_m_Y') . '.xlsx');
    }
}
