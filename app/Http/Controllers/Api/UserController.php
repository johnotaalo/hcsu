<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use \App\User;
use \App\Enums\UserType;

class UserController extends Controller
{
    function __construct(){
    }

    function getUsers(Request $request){
    	$searchQueries = $request->get('normalSearch');
        $activeFilter = $request->get('activeStaffSearch');
        $userTypeFilter = $request->get('userTypeFilter');
        $limit = $request->get('limit');
        $page = $request->get('page');
        $ascending = $request->get('ascending');
        $byColumn = $request->get('byColumn');
        $orderBy = $request->get('orderBy');

        $queryBuilder = User::select('id', 'name', 'username', 'user_type', 'email', 'created_at');

        if ($searchQueries) {
        	$queryBuilder->where('name', 'LIKE', "%{$searchQueries}%");
        	$queryBuilder->orWhere('username', 'LIKE', "%{$searchQueries}%");
        	$queryBuilder->orWhere('email', 'LIKE', "%{$searchQueries}%");
        }


        if($userTypeFilter != "all" && $userTypeFilter != ""){
            $queryBuilder->where('user_type', $userTypeFilter);
        }



        if (is_null($userTypeFilter)) {
            $instance = \App\Enums\UserType::fromValue($userTypeFilter);
            $queryBuilder->where("user_type", "");
        }

        $count = $queryBuilder->count();

        $queryBuilder = $queryBuilder->limit($limit)->skip($limit * ($page - 1));

        $users = $queryBuilder->get();

    	return [
    		'data' 	=> $users,
    		'count'	=>	$count
    	];
    }

    function getUserTypes(Request $request){
    	return UserType::toArray();
    }

    function store(Request $request){
    	$user = $request->validate([
    		"firstname"	=>	"required",
    		"lastname"	=>	"required",
    		"email"		=>	"required|unique:users",
    		'username'	=>	"required|unique:users"
    	]);

    	$user = new User();

    	$user->name = strtoupper($request->input('lastname')) . ", " . ucfirst(strtolower($request->input('firstname')));
    	$user->email = $request->input('email');
    	$user->password = (null !== $request->input('password')) ? \Hash::make($request->input('password')) : "0000000000000000000000";
    	$user->username = $request->input("username");
    	$user->user_type = $request->input("user_type");

    	$user->save();

    	return $user;
    }

    function getDisputes(Request $request){
        return \App\Models\UserDispute::where('merged', 0)->get();
    }

    function confirmClient(Request $request){
        return ['success' => true];
    }
}
