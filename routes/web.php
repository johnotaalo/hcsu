<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/photos/principal/{host_country_id}', function($host_country_id){
	$principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $host_country_id)->first();
	$filename = $principal->IMAGE;

	if (!\Storage::exists($filename)) {
		// die("No file name");
		// abort(404);
		$file = public_path('images/no_avatar.svg');
		$type = \File::mimeType($file);

		$headers = array('Content-Type: ' . $type);

		return Response::download($file, 'no_avatar.svg',$headers);
	}

	$file = \Storage::get($filename);
	$type = \Storage::mimeType($filename);

	$response = Response::make($file, 200);
	$response->header("Content-Type", $type);

	return $response;
})->name('principal-photo');

Route::get('/photos/dependent/{host_country_id}', function($host_country_id){
	$dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $host_country_id)->first();
	$filename = $dependent->IMAGE;

	if (!\Storage::exists($filename)) {
		// die("No file name");
		// abort(404);
		$file = public_path('images/no_avatar.svg');
		$type = \File::mimeType($file);

		$headers = array('Content-Type: ' . $type);

		return Response::download($file, 'no_avatar.svg',$headers);
	}

	$file = \Storage::get($filename);
	$type = \Storage::mimeType($filename);

	$response = Response::make($file, 200);
	$response->header("Content-Type", $type);

	return $response;
})->name('dependent-photo');
Route::get('/sample', 'Test\SampleController@index');
Route::get('/pmauthentication', 'Test\SampleController@pmauth');
Route::get('/docusign', 'Api\DocusignAPIController@index')->name('docusign-client');
Route::get('/docusign/login', 'Api\DocusignAPIController@login')->name('docusign-client-login');
Route::get('/docusign/callback', 'Api\DocusignAPIController@loginCallback');
Route::get('/docusign/afterSignature/{case}/{process}/{task}', 'Api\DocusignAPIController@afterSignature')->name('after-signature');
Route::get('/docusign/document-sign', 'Api\DocusignAPIController@generateSigningDocument');
Route::get('/docusign/document-download/{envelope_id}', 'Api\DocusignAPIController@downloadDocument')->name('download-docusigned-doc');

Route::prefix('focal-point')->group(function(){
	Route::get('/', 'FocalPoints\DashboardController@index')->name('focalpoints-home');
	Route::get('/login', 'Auth\FocalPointAuthController@login')->name('focalpoints-login');
	Route::get('/password/reset/{token}', 'Auth\FocalPointAuthController@resetPassword')->name('focalpoint-reset-password');
	Route::post('/password/reset/{token}', 'Auth\FocalPointAuthController@changePassword');
});

Auth::routes();

Route::get('/{any}', 'Api\AppController@index')->where('any', '.*')->name('default');



Route::get('/home', 'HomeController@index')->name('home');
