<?php

namespace App\Http\Controllers\FocalPoints;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Jobs\GenerateClientReport;

class ReportsController extends Controller
{
    function download(Request $request){
        $request->validate([
            'action'    =>  'required|in:email,download'
        ]);

        $action = $request->action;

        if ($action == "download"){
            $exportData = GenerateClientReport::dispatchNow($request->input(), \Auth::user());
            return \Excel::download($exportData, "Organization Data.xlsx");
        }

        GenerateClientReport::dispatch($request->input(), \Auth::user());

        return ['type'  =>  'success', "message"    =>  'Successfully dispatched report generation'];
    }
}
