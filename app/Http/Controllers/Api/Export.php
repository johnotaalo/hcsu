<?php
namespace App\Http\Controllers\API;
ini_set('max_execution_time', 500);
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Export extends Controller
{
    function exportVAT(Request $request){
        $app_numbers = (\App\Models\PM\Application::where('APP_STATUS', 'TO_DO')->pluck('APP_NUMBER'))->toArray();
    	$vatQuery = \App\Models\VAT::orderBy('CASE_NO','DESC')
                ->whereIn('CASE_NO', $app_numbers);
        $vat = $vatQuery->get();

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

    function exportNewProcessData(Request $request){
        $processes = collect($request->input('processes'))->map(function($process){
            return $process['id'];
        })->toArray();
        $agencies = collect($request->input('agencies'))->map(function($agency){
            return $agency['id'];
        })->toArray();
        $agencies_list = (count($agencies) > 0) ? implode(",", $agencies) : "All";

        $statuses = collect($request->input('status'))->map(function($status){
            return $status['id'];
        })->toArray();

        $years = $request->years;

        $queryBuilder = \DB::connection('pm_data')->table('VW_CASE_INFO');
        if (count($processes) > 0) {
            $queryBuilder = $queryBuilder->whereIn('PRO_UID', $processes);
        }

        if (count($agencies) > 0) {
            $queryBuilder = $queryBuilder->whereIn('AGENCY', $agencies);
        }

        if(count($statuses)){
            $queryBuilder = $queryBuilder->whereIn('CASE_STATUS', $statuses);
        }

        if(count($years)){
            $queryBuilder = $queryBuilder->whereIn(\DB::raw('YEAR(CASE_START_DATE)'), $years);
        }

        $data = $queryBuilder->get();
        $exportData = new \App\Exports\OrganizationDataExport($data);
        return \Excel::download($exportData, "Organization Data ({$agencies_list}).xlsx");
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
                'ACRONYM'               =>  $data->vatObj->supplier->ABBREV,
                'PIN'                   =>  $data->vatObj->supplier->PIN,
                'SUPPLIER_NAME'         =>  $data->vatObj->supplier->NAME,
                'ACCOUNT NO'            =>  $data->vatObj->ACCOUNT_NO,
                'YEAR'                  =>  \Carbon\Carbon::parse($data->vatObj->DURATION_TO)->format('Y')
            ];
        });

        $supplierList = [];

        foreach ($list as $key => $value) {
           $supplierList[$value['ACRONYM']][] = [
                'NO'                    =>  $key + 1,
                'ORGANIZAITON'          =>  $value['ORGANIZAITON'],
                'CASE_NO'               =>  $value['CASE_NO'],
                'DATE_APPLIED_AT_MFA'   =>  $value['DATE_APPLIED_AT_MFA'],
                'DATE_APPLIED_AT_KRA'   =>  $value['DATE_APPLIED_AT_KRA'],
                'DATE_APPROVED'         =>  $value['DATE_APPROVED'],
                'PIN'                   =>  $value['PIN'],
                'SUPPLIER_NAME'         =>  $value['SUPPLIER_NAME'],
                'ACCOUNT NO'            =>  $value['ACCOUNT NO'],
                'YEAR'                  =>  $value['YEAR']
           ];
        }

        // 
        $suppliers = array_keys($supplierList);
        // dd($suppliers);
        $supplierList = collect($supplierList);

        $exportData = new \App\Exports\BlanketVATExport($suppliers, $supplierList, $batch);
        return \Excel::download($exportData, 'Blanket VAT List for Batch: ' . \Carbon\Carbon::parse($batch->batch_date)->format('d_m_Y') . '.xlsx');
    }
}
