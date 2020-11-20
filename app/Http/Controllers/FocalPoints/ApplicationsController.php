<?php

namespace App\Http\Controllers\FocalPoints;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplicationsController extends Controller
{
    function get(Request $request){
    	$searchQueries = $request->get('normalSearch');
        $limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $queryBuilder = \App\Models\UserApplications::select('*');

        if ($searchQueries) {
        	$queryBuilder->where('PROCESS_UID', 'LIKE', "%{$searchQueries}%");
        }

        $count = $queryBuilder->count();
        $queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));

        $data = $queryBuilder->get();
    	return [
    		'count'	=>	$count,
    		'data'	=>	$data
    	];
    }

    function new(Request $request){
    	$input = $request->input();
    	// dd($request->hasFile('uploads'));
    	if ($request->input('process') == null) {
    		$input["process"] =  [
    			"prj_uid" => "1107820965eb2b13bc0c284023169236"
    		];
    	}

    	$application = \App\Models\UserApplications::create([
    		'PROCESS_UID'			=>	$input['process']['prj_uid'],
    		'HOST_COUNTRY_ID'		=>	'10001000',
    		'COMMENT'				=>	$input['comment'],
    		'SUBMITTED_BY'			=>	\Auth::user()->id,
    		'AUTHENTICATION_SOURCE'	=>	'USER'
    	]);


    	$file = new \App\Model\UserApplicationFile();

    	$file->USER_APPLICATION_ID = $application->id;
    	$file->FILE_URL = $this->uploadFile($request->file('uploads'));

    	$file->save();
    }

    function uploadFile($file){
    	$path = $file->store('user_applications');

    	return $path;
    }
}
