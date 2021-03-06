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
	if($principal){
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
	}
})->name('principal-photo');
Route::get('uploads/{id}', function($id){
	$filename = \App\Model\UserApplicationFile::where('id', $id)->first();

	if (\Storage::exists($filename->FILE_URL)) {
		$file = \Storage::get($filename->FILE_URL);
		$type = \Storage::mimeType($filename->FILE_URL);

		$response = Response::make($file, 200);
		$response->header("Content-Type", $type);

		return $response;
	}

});

Route::get('agency/logo/{id}', function($id){
	$agency = \App\Models\Agency::findOrFail($id);

	if (\Storage::exists($agency->logo_link)) {
		$file = \Storage::get($agency->logo_link);
		$type = \Storage::mimeType($agency->logo_link);

		$response = Response::make($file, 200);
		$response->header("Content-Type", $type);

		return $response;
	}
});

Route::get('vehicle/plates/returned/download/list/signed/{id}' , 'Api\VehicleController@downloadSignedList');

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

Route::get('/uploads/vat/{upload_id}', function($upload_id){
	$file = \App\VATUserApplicationDocument::find($upload_id);
	// dd($file->document_link);
	if (\Storage::exists($file->document_link)) {
		$filex = \Storage::get($file->document_link);
		$type = \Storage::mimeType($file->document_link);

		$response = Response::make($filex, 200);
		$response->header("Content-Type", $type);

		return $response;
	}
	abort(404);
});

Route::get('/templates/file/{template_id}', function($template_id){
	$file = \App\FormTemplate::find($template_id);

	if (\Storage::exists($file->path)) {
		$filex = \Storage::get($file->path);
		$type = \Storage::mimeType($file->path);

		$response = Response::make($filex, 200);
		$response->header("Content-Type", $type);

		return $response;
	}
	abort(404);
});

Route::get('/sample', 'Test\SampleController@index');
Route::get('/pmauthentication', 'Test\SampleController@pmauth');
Route::get('/docusign', 'Api\DocusignAPIController@index')->name('docusign-client');
Route::get('/docusign/login', 'Api\DocusignAPIController@login')->name('docusign-client-login');
Route::get('/docusign/callback', 'Api\DocusignAPIController@loginCallback');
Route::get('/docusign/afterSignature/{case}/{process}/{task}', 'Api\DocusignAPIController@afterSignature')->name('after-signature');
Route::get('/docusign/document-sign', 'Api\DocusignAPIController@generateSigningDocument');
Route::get('/docusign/document-download/{envelope_id}', 'Api\DocusignAPIController@downloadDocument')->name('download-docusigned-doc');

Route::prefix('adobe-sign')->group(function(){
	Route::get('test', 'Api\AdobeSignApiController@test');
	Route::get('test-mofa', 'Api\AdobeSignApiController@testMofa');
	Route::get('callback', 'Api\AdobeSignApiController@callback');
	Route::get('auth-callback', 'Api\AdobeSignApiController@authCallback');
	Route::get('test-urls', 'Api\AdobeSignApiController@TestURL');
	Route::get('get-signing-urls', 'Api\AdobeSignApiController@getSigningURLs');
	Route::get('library-documents', 'Api\AdobeSignApiController@libraryDocuments');
	Route::get('download/{id}', 'Api\AppController@downloadSignedApplication');
});

Route::middleware('auth')->prefix('focal-point')->group(function(){
	Route::get('/', 'FocalPoints\DashboardController@index')->name('focalpoints-home');
	Route::get('/login', 'Auth\FocalPointAuthController@login')->name('focalpoints-login');
	Route::get('/password/reset/{token}', 'Auth\FocalPointAuthController@resetPassword')->name('focalpoint-reset-password');
	Route::post('/password/reset/{token}', 'Auth\FocalPointAuthController@changePassword');

	Route::prefix('vat')->group(function(){
		Route::prefix('download')->group(function(){
			Route::get('acknowledgement/{id}', 'FocalPoints\VATController@downloadAcknowledgement');
		});
	});
});

Route::middleware('auth')->prefix('clients')->group(function(){
	Route::get('/', 'FocalPoints\DashboardController@index')->name('clients-home');
	Route::get('/confirm/{id}', 'FocalPoints\DashboardController@confirmPrincipal')->name('clients-confirm');
	Route::post('/confirm', 'FocalPoints\DashboardController@confirmUser')->name('confirm-user');
	Route::get('/confirmation/details/{id}', 'FocalPoints\DashboardController@confirmationDetails')->name('confirmation-cancel');
	Route::post('confirmation/dispute', 'FocalPoints\DashboardController@storeDispute')->name('confirmation-dispute');
});

Route::get('pages/noaction', function(){
	return view('pages.adobesign.no_action_required');
});

Route::get('/adobesign/signature/{agreementId}/{managerEmail}', 'Api\AdobeSignApiController@getDocumentSigningURL');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'Api\AppController@index');

// Route::get('/{any}', 'Api\AppController@index')->where('any', '.*')->name('default');
// Route::group(['middleware' => 'web, ldap'], function(){
	Route::get('/{any}', 'Api\AppController@index')
		->where('any', '.*')
		->name('default');
	// });
