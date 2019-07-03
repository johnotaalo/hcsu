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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
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

Route::prefix('client')->group(function(){
	Route::post('/update', 'Api\AppController@updateHostCountryID');
});

Route::prefix('agencies')->group(function(){
	Route::get('/', 'Api\AppController@getAgencies');
	Route::get('/search', 'Api\AgenciesController@searchAgencies');
	Route::post('/add', 'Api\AgenciesController@addAgencies');
});

Route::get('passport-types', 'Api\AppController@getPassportTypes');
Route::get('countries', 'Api\AppController@getCountries');
Route::prefix('data')->group(function(){
	Route::get('/principal-options', 'Api\AppController@getPrincipalOptions');
});

Route::prefix('vehicle')->group(function(){
	Route::get('/', 'Api\VehicleController@getVehicles');
	Route::get('/{host_country_id}', 'Api\VehicleController@getVehicles');
});
