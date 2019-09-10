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
	Route::group(['middleware' => 'auth:api'], function(){
		Route::get('details', 'Auth\AuthController@details');
	});
});

Route::get('test', function(){
	$faker = \Faker\Factory::create();
	$tests = \App\Test::insert([
		['name' => $faker->name],
		['name' => $faker->name]
	]);

	dd($tests);
});

Route::group(['middleware' => 'auth:api'], function(){
	Route::prefix('principal')->group(function(){
		Route::get('/', 'Api\PrincipalController@getPrincipal');
		Route::post('/add', 'Api\PrincipalController@add');
		Route::get('/get/{id}', 'Api\PrincipalController@get');
		Route::post('/update', 'Api\PrincipalController@update');

		Route::post('/passport/add', 'Api\PrincipalController@addPassport');
		Route::post('/passport/edit', 'Api\PrincipalController@editPassport');
		Route::delete('/passport', 'Api\PrincipalController@deletePassport');

		Route::post('/contract/add', 'Api\PrincipalController@addContract');
		Route::post('/contract/edit', 'Api\PrincipalController@editContract');
		Route::delete('/contract', 'Api\PrincipalController@deleteContract');

		Route::post('/dependent/add', 'Api\PrincipalController@addDependent');
		Route::post('/dependent/edit', 'Api\PrincipalController@editDependent');
		Route::delete('/dependent', 'Api\PrincipalController@deleteDependent');

		Route::get('/search', 'Api\PrincipalController@searchPrincipal');
	});
});

Route::prefix('principal')->group(function(){
	Route::get('/', 'Api\PrincipalController@getPrincipal');
	Route::post('/add', 'Api\PrincipalController@add');
	Route::get('/get/{id}', 'Api\PrincipalController@get');
	Route::post('/update', 'Api\PrincipalController@update');

	Route::post('/passport/add', 'Api\PrincipalController@addPassport');
	Route::post('/passport/edit', 'Api\PrincipalController@editPassport');
	Route::delete('/passport', 'Api\PrincipalController@deletePassport');

	Route::post('/contract/add', 'Api\PrincipalController@addContract');
	Route::post('/contract/edit', 'Api\PrincipalController@editContract');
	Route::delete('/contract', 'Api\PrincipalController@deleteContract');

	Route::post('/dependent/add', 'Api\PrincipalController@addDependent');
	Route::post('/dependent/edit', 'Api\PrincipalController@editDependent');
	Route::delete('/dependent', 'Api\PrincipalController@deleteDependent');

	Route::get('/search', 'Api\PrincipalController@searchPrincipal');
});

Route::prefix('template')->group(function(){
	Route::post('/add', 'Api\AppController@addTemplate');
	Route::get('/nv/data-fields', 'Api\AppController@getNVDataFields');
});

Route::prefix('client')->group(function(){
	Route::post('/update', 'Api\AppController@updateHostCountryID');
});

Route::prefix('agencies')->group(function(){
	Route::get('/', 'Api\AppController@getAgencies');
	Route::get('/search', 'Api\AgenciesController@searchAgencies');
	Route::post('/add', 'Api\AgenciesController@addAgencies');
	Route::get('/get/{agency_id}', 'Api\AgenciesController@getAgency');
});

Route::get('passport-types', 'Api\AppController@getPassportTypes');
Route::get('countries', 'Api\AppController@getCountries');
Route::prefix('data')->group(function(){
	Route::get('/principal-options', 'Api\AppController@getPrincipalOptions');
});

Route::prefix('vehicle')->group(function(){
	Route::get('/', 'Api\VehicleController@getVehicles');
	Route::get('/{host_country_id}', 'Api\VehicleController@getVehicles');

	Route::get('plates/agency/prefixes', 'Api\VehicleController@getPrefixes');
	Route::post('plates/prefix', 'Api\VehicleController@addPrefix');
	Route::put('plates/prefix', 'Api\VehicleController@updatePrefix');

	Route::post('plates/organization/prefix', 'Api\VehicleController@addOrganizationPrefix');
	Route::delete('plates/organization/prefix/{id}', 'Api\VehicleController@removeOrganizationPrefix');
});

Route::prefix('data')->group(function(){
	Route::prefix('suppliers')->group(function(){
		Route::post('/', 'Api\SupplierController@create');
		Route::get('/all', 'Api\SupplierController@all');
		Route::get('/search', 'Api\SupplierController@searchSupplier');
	});

	Route::prefix('management')->group(function(){
		Route::get('/import', 'Api\DataController@importData');
	});

	Route::prefix('dependent')->group(function(){
		Route::get('/get/{host_country_id}', 'Api\PrincipalController@getDependent');
	});

	Route::prefix('processes')->group(function(){
		Route::get('/', 'Api\AppController@getProcessList');
		Route::get('/{process}/tasks', 'Api\AppController@getProcessTasks');
	});
});

Route::prefix('documents')->group(function(){
	Route::get('/generate/{case_no}', 'Api\AppController@generateDocument');
	Route::get('/get/note_verbal/{process}/{case_no}', 'Api\AppController@generateNoteVerbal');

	Route::get('/export/vat/normal/list', 'Api\Export@exportVAT');
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
		});
	});
});