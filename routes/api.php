<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::prefix('auth')->group(function(){
// 	Route::post('login', 'Auth\AuthController@login');
// 	Route::get('refresh', 'Auth\AuthController@refresh');
// 	Route::group(['middleware' => 'auth:api'], function(){
// 		Route::get('user', 'Auth\AuthController@user');
//         Route::post('logout', 'Auth\AuthController@logout');
// 	});
// 	Route::middleware('auth:api')->get('/user', function (Request $request) {
// 	    return $request->user();
// 	});
// });

Route::prefix('auth')->group(function(){
	Route::post('login', 'Auth\AuthController@login');
	Route::get('details/{type?}/{user}', 'Auth\AuthController@details');
	Route::group(['middleware' => 'auth:api'], function(){
		Route::get('details', 'Auth\AuthController@details');
	});
});

Route::prefix('kiosk')->group(function(){
	Route::get('clients/search', 'Api\AppController@searchClient');
	Route::post('client/session/validate', 'Api\AppController@validateSession');
});

Route::get('test', function(){
	// $faker = \Faker\Factory::create();
	// $tests = \App\Test::insert([
	// 	['name' => $faker->name],
	// 	['name' => $faker->name]
	// ]);

	// dd($tests);
	$plates = \App\ReturnedPlate::with('plates')->get();
	return $plates;
});

Route::get('/test/queue', function(){
	$organization_groups = [
        'UNSOS' => [
            'UNSOS',
            'UNSOS (SO)',
            'UNSOA',
            'UNSOM'
        ],
        'UNICEF'    =>  [
            'UNICEF (ESARO) (RO)',
            'UNICEF (KCO)',
            'UNICEF USSC SO'
        ]
    ];

    foreach ($organization_groups as $group => $organizations) {
        \App\Jobs\ExportOrganizationData::dispatch($organizations, $group)->onQueue('processing');
    }
});

// Route::group(['middleware' => 'auth:api'], function(){
// 	Route::prefix('principal')->group(function(){
// 		Route::get('/', 'Api\PrincipalController@getPrincipal');
// 		Route::post('/add', 'Api\PrincipalController@add');
// 		Route::get('/get/{id}', 'Api\PrincipalController@get');
// 		Route::post('/update', 'Api\PrincipalController@update');

// 		Route::post('/passport/add', 'Api\PrincipalController@addPassport');
// 		Route::post('/passport/edit', 'Api\PrincipalController@editPassport');
// 		Route::delete('/passport', 'Api\PrincipalController@deletePassport');

// 		Route::post('/contract/add', 'Api\PrincipalController@addContract');
// 		Route::post('/contract/edit', 'Api\PrincipalController@editContract');
// 		Route::delete('/contract', 'Api\PrincipalController@deleteContract');

// 		Route::post('/dependent/add', 'Api\PrincipalController@addDependent');
// 		Route::post('/dependent/edit', 'Api\PrincipalController@editDependent');
// 		Route::delete('/dependent', 'Api\PrincipalController@deleteDependent');

// 		Route::get('/search', 'Api\PrincipalController@searchPrincipal');
// 	});
// });

Route::prefix('principal')->group(function(){
	Route::get('/', 'Api\PrincipalController@getPrincipal');
	Route::post('/add', 'Api\PrincipalController@add');
	Route::get('/get/{id}', 'Api\PrincipalController@get');
	Route::post('/update', 'Api\PrincipalController@update');
	Route::put('/{host_country_id}/activate', 'Api\PrincipalController@activateClient');

	Route::post('preflight/search', 'Api\PrincipalController@searchPreflight');

	Route::post('/passport/add', 'Api\PrincipalController@addPassport');
	Route::post('/passport/edit', 'Api\PrincipalController@editPassport');
	Route::delete('/passport', 'Api\PrincipalController@deletePassport');

	Route::post('/contract/add', 'Api\PrincipalController@addContract');
	Route::post('/contract/edit', 'Api\PrincipalController@editContract');
	Route::delete('/contract', 'Api\PrincipalController@deleteContract');

	Route::post('/dependent/add', 'Api\PrincipalController@addDependent');
	Route::post('/dependent/edit', 'Api\PrincipalController@editDependent');
	Route::delete('/dependent', 'Api\PrincipalController@deleteDependent');
	Route::get('/dependent/list', 'Api\PrincipalController@getDependents');

	Route::get('/search', 'Api\PrincipalController@searchPrincipal');

	Route::post('domesticworker/add', 'Api\PrincipalController@addDomesticWorker');
	Route::put('domesticworker/edit', 'Api\PrincipalController@updateDomesticWorker');
	Route::delete('domesticworker', 'Api\PrincipalController@deleteDomesticWorker');
	Route::get('domesticworker/search', 'Api\PrincipalController@searchDomesticWorker');
	Route::get('domesticworker/get/{host_country_id}', 'Api\PrincipalController@getDomesticWorker');
});

Route::prefix('template')->group(function(){
	Route::get('/get/{id}', 'Api\AppController@getTemplate');
	Route::get("/list", 'Api\AppController@getTemplateList');
	Route::post('/add', 'Api\AppController@addTemplate');
	Route::put('/edit', 'Api\AppController@editTemplate');
	Route::get('/nv/data-fields', 'Api\AppController@getNVDataFields');
});

Route::prefix('client')->group(function(){
	Route::post('/update', 'Api\AppController@updateHostCountryID');
	Route::get("/search", 'Api\AppController@searchAllClients');
});

Route::prefix('agencies')->group(function(){
	Route::get('/', 'Api\AppController@getAgencies');
	Route::get('/search', 'Api\AgenciesController@searchAgencies');
	Route::post('/add', 'Api\AgenciesController@addAgencies');
	Route::get('/get/{agency_id}', 'Api\AgenciesController@getAgency');
	Route::put('/update/{agency_id}', 'Api\AgenciesController@updateAgency');
	Route::get("/all", "Api\AgenciesController@all");
	Route::post("/focal-point/mapping/{id}", "Api\AgenciesController@storeMapping");
    Route::delete("/focal-point/mapping", "Api\AgenciesController@removeMapping");
});


Route::prefix('agency')->group(function(){
	Route::get('focal-point/{host_country_id}', 'Api\AgenciesController@getFocalPoints');
});

Route::get('passport-types', 'Api\AppController@getPassportTypes');
Route::get('countries', 'Api\AppController@getCountries');
Route::prefix('data')->group(function(){
	Route::get('/principal-options', 'Api\AppController@getPrincipalOptions');
});

Route::prefix('vehicle')->group(function(){
	Route::get('/', 'Api\VehicleController@getVehicles');
	Route::get('/{host_country_id}', 'Api\VehicleController@getVehicles');
	Route::get('/get/{id}/{type}', 'Api\VehicleController@getVehicleDetails');
	Route::get('plates/agency/prefixes', 'Api\VehicleController@getPrefixes');
	Route::post('plates/prefix', 'Api\VehicleController@addPrefix');
	Route::put('plates/prefix', 'Api\VehicleController@updatePrefix');
	Route::post('plates/add', 'Api\VehicleController@addPlate');
	Route::post('plates/add/bulk', 'Api\VehicleController@bulkPlates');
	Route::get('plates/list', 'Api\VehicleController@getPlatesList');

	Route::post('plates/organization/prefix', 'Api\VehicleController@addOrganizationPrefix');
	Route::delete('plates/organization/prefix/{id}', 'Api\VehicleController@removeOrganizationPrefix');

	Route::prefix('plates/returned')->group(function(){
		Route::post('create', 'Api\VehicleController@createRNPList');
		Route::put('edit', 'Api\VehicleController@editRNPList');
		Route::get('list', 'Api\VehicleController@searchRNP');
		Route::get('list/{id}', 'Api\VehicleController@getRNP');
		Route::post('upload/list/signed', 'Api\VehicleController@uploadSignedList');
		// Route::get('download/list/signed/{id}', 'Api\VehicleController@downloadSignedList');
		Route::get('download/list/unsigned/{id}', 'Api\VehicleController@downloadUnsignedList');
	});

	Route::prefix("/form-a")->group(function(){
		Route::post("search", "Api\VehicleController@searchFormA");
		Route::post("create-logbook-case/{form_a}", "Api\VehicleController@createLogbookCase");
	});
});

Route::prefix('data')->group(function(){
	Route::prefix('suppliers')->group(function(){
		Route::post('/', 'Api\SupplierController@create');
		Route::put('/', 'Api\SupplierController@update');
		Route::get('/all', 'Api\SupplierController@all');
		Route::get('/search', 'Api\SupplierController@searchSupplier');
	});

	Route::prefix('clearing-agents')->group(function(){
		Route::post('/', 'Api\ClearingAgentsController@create');
		Route::put('/', 'Api\ClearingAgentsController@update');
		Route::get('/all', 'Api\ClearingAgentsController@all');
	});

	Route::prefix('designations')->group(function(){
		Route::post('/', 'Api\DataController@addDesignation');
	});

	Route::prefix('management')->group(function(){
		Route::prefix('pending')->group(function(){
			Route::get('principals', 'Api\DataController@pendingPrincipals');
		});
		Route::get('/import', 'Api\DataController@importData');
		Route::get('/import/pending/staff/{record_id}', 'Api\DataController@importPendingStaff');
		Route::get('/import/dependents/spouse', 'Api\DataController@importSpouseData');
		Route::get('import/principal/nationalities', 'Api\DataController@importPrincipalNationalities');
		Route::get('import/dependents/passports', 'Api\DataController@importDependentPassports');
	});

	Route::prefix('dependent')->group(function(){
		Route::get('/get/{host_country_id}', 'Api\PrincipalController@getDependent');
		Route::post('/update/{host_country_id}', 'Api\PrincipalController@updateDependent');
		Route::post('/passport/add/{host_country_id}', 'Api\PrincipalController@addDependentPassport');
		Route::post('/passport/edit/{passport_id}', 'Api\PrincipalController@editDependentPassport');
		Route::delete('/passport/delete/{passport_id}', 'Api\PrincipalController@deleteDependentPassport');
	});

	Route::prefix('processes')->group(function(){
		Route::get('/', 'Api\AppController@getProcessList');
		Route::get('/local', 'Api\AppController@getLocalProcessList');
		Route::get('/{process}/tasks', 'Api\AppController@getProcessTasks');
		Route::get('/{process}/task/{task}/steps', 'Api\AppController@getTaskSteps');

		Route::get('/ipmis', 'Api\AppController@ipmisSubprocesses');
		Route::get('/ipmis/functionality', 'Api\AppController@ipmisFunctionality');
		Route::put('/ipmis/functionality/{id}', 'Api\AppController@toggleIPMISFunctionality');

		Route::get('oldpm/search/case/{case_no}', 'Api\AppController@searchOldPMCase');
		Route::get('oldpm/users', 'Api\AppController@getOLDPMUsers');
		Route::post('oldpm/reassign', 'Api\AppController@reassignOLDPMCase');
	});

	Route::prefix('applications')->group(function(){
		Route::get('oldestdate', 'Api\AppController@getOldestDate');
	});

	Route::prefix('options')->group(function(){
		Route::get('/dependent', 'Api\DataController@getDependentOptions');
		Route::get('/domestic-worker', 'Api\DataController@getDomesticWorkerOptions');
	});

	Route::prefix('users')->group(function(){
		Route::get('/', 'Api\AppController@getUsers');
	});

	Route::get('nationalities', function(){
		return \App\Models\Country::all();
	});

	Route::prefix('tims')->group(function(){
		Route::get('/list', 'Api\VehicleController@searchTims');
		Route::post('/registration', 'Api\VehicleController@registerTims');
	});

	Route::prefix('adobe-sign')->group(function(){
		Route::get('/signatories', 'Api\AdobeSignApiController@getSignatories');
		Route::post('/signatories', 'Api\AdobeSignApiController@addSignatory');
		Route::put('/signatories', 'Api\AdobeSignApiController@updateSignatory');
		Route::get('/signatories/get/{id}', 'Api\AdobeSignApiController@getSignatory');

		Route::get('/documents', 'Api\AdobeSignApiController@getDocuments');
	});


	Route::prefix('revalidation')->group(function(){
		Route::get("{host_country_id}/{form_type}/{system}", 'Api\AppController@getRevalidationCases');
	});
});

Route::prefix('documents')->group(function(){
	Route::get('/generate/{case_no}', 'Api\AppController@generateDocument');
	Route::prefix('adobe-sign')->group(function(){
		Route::get('for-signature/{case_no}', 'Api\AdobeSignApiController@submitDocumentsForSigning');
		Route::post('send', 'Api\AdobeSignApiController@sendDocumentForSignature');
		Route::post('test-mofa', 'Api\AdobeSignApiController@testMofa');
		Route::get('library-documents', 'Api\AdobeSignApiController@libraryDocuments');
	});
	Route::get('/generate/NOA/{host_country_id}', 'Api\AppController@generateNOA');
	Route::get('/get/note_verbal/{process}/{case_no}', 'Api\AppController@generateNoteVerbal');

	Route::get('/export/vat/normal/list', 'Api\Export@exportVAT');
	Route::post('/export/processes/organization', 'Api\Export@exportNewProcessData');
});

Route::get('/dependent/search', 'Api\PrincipalController@searchDependent');

Route::prefix('/vat')->group(function(){
	Route::prefix('blanket')->group(function(){
		Route::get('batches', 'Api\VATController@getBlanketBatches');
		Route::post('batch', 'Api\VATController@addBlanketBatch');
		Route::delete('batch', 'Api\VATController@deleteBlanketBatch');

		Route::get('list/download/{batch}', 'Api\Export@downloadBlanketVATList');
	});
});

Route::prefix('/focal-points')->group(function(){
	Route::group(['middleware' => 'auth:api'], function(){
		Route::prefix('vat')->group(function(){
			Route::post('/', 'FocalPoints\VATController@addVATApplication');
			Route::put('/', 'FocalPoints\VATController@updateVATApplication');
			Route::get('/search', 'FocalPoints\VATController@searchApplication');
			Route::get('/user-application/{id}', 'FocalPoints\VATController@getVATApplication');
		});

		Route::prefix('applications')->group(function(){
			Route::get('/', 'FocalPoints\ApplicationsController@get');
			Route::get('all', 'FocalPoints\ApplicationsController@getAllApplications');
			Route::get('/users', 'FocalPoints\ApplicationsController@getUsers');
			Route::post('/assign/{id}', 'FocalPoints\ApplicationsController@assign');
			Route::get('/get/{id}', 'FocalPoints\ApplicationsController@getApplication');
			Route::post('/new', 'FocalPoints\ApplicationsController@new');
			Route::put('/edit', 'FocalPoints\ApplicationsController@edit');
			Route::get('/cancel/{id}', 'FocalPoints\ApplicationsController@cancelApplication');
		});

		Route::prefix('report')->group(function(){
		    Route::post("/download", "FocalPoints\ReportsController@download");
        });

		Route::get('/', 'Api\AgenciesController@getAllFocalpoints');
	});
});

Route::prefix('processmaker')->group(function(){
	Route::get('/case/{case_no}/variable/{variable}', 'Api\AppController@getCaseVariable');
});

Route::get('user/reset/{id}', 'Api\AgenciesController@sendResetPassword');

Route::prefix('/other/clients')->group(function(){
	Route::get('/', 'Api\OtherClientsController@index');
	Route::post('/', 'Api\OtherClientsController@store');
	Route::put('/', 'Api\OtherClientsController@update');
	Route::get('/get/{host_country_id}', 'Api\OtherClientsController@getClient');
	Route::get('/search', 'Api\OtherClientsController@search');
});

Route::prefix('adobe-sign')->group(function(){
	Route::get('get-signing-urls', 'Api\AdobeSignApiController@testHook');
	Route::post('get-signing-urls', 'Api\AdobeSignApiController@getSigningURLs');
});

Route::prefix('users')->middleware('auth:api')->group(function(){
	Route::get('/all', 'Api\UserController@getUsers');
	Route::get('/usertypes', 'Api\UserController@getUserTypes');
	Route::post("/add", "Api\UserController@store");
	Route::get("/disputes", "Api\UserController@getDisputes");
});

Route::prefix('control-form')->group(function(){
    Route::post("/", "Api\ControlFormController@store");
    Route::get("/test/{id}", "Api\ControlFormController@test");
});
