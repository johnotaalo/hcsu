<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SubmissionControlForm;

use App\Jobs\SendDocumentControlForm;

class ControlFormController extends Controller
{
    function store(Request $request){
        $controlForm = SubmissionControlForm::create([
            'CASE_NO'           =>  $request->case_no,
            'HOST_COUNTRY_ID'   =>  $request->host_country_id,
            'DOCUMENTS'         =>  $request->documents,
            'USER_UID'          =>  $request->user
        ]);

        SendDocumentControlForm::dispatch($controlForm);
        return $controlForm;
    }

    function test(Request $request){
        $form = SubmissionControlForm::find($request->id);

        $data = [
            "Invoice",
            "Bill of Lading",
            "Certificate of Road Worthiness",
            "Export Certificate",
            "KRA PIN",
        ];
//        dd($form->application);
        SendDocumentControlForm::dispatch($form);
    }
}
