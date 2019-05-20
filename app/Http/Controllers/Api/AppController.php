<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\models\Agency;
use App\models\PassportType;
use App\models\Country;
use App\models\ContractType;
use App\models\Grade;
use App\models\Relationship;
use App\models\ContractDesignation;

class AppController extends Controller
{
    function index(){
    	return view('app.main');
    }

    function getAgencies(){
    	return Agency::all();
    }

    function getPassportTypes(){
    	return PassportType::all();
    }

    function getCountries(){
    	return Country::all();
    }

    function getContractTypes(){
    	return ContractType::all();
    }

    function getGrades(){
    	return Grade::orderBy('order', 'ASC')->get();
    }

    function getRelationships(){
    	return Relationship::all();
    }

    function getDesignations(){
        return ContractDesignation::all();
    }

    function getPrincipalOptions(){
    	$response = [];

    	$response['countries'] = $this->getCountries();
    	$response['agencies'] = $this->getAgencies();
    	$response['passportTypes'] = $this->getPassportTypes();
    	$response['contractTypes'] = $this->getContractTypes();
    	$response['grades'] = $this->getGrades();
    	$response['relationships'] = $this->getRelationships();
        $response['designations'] = $this->getDesignations();

    	return $response;
    }
}
