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


Route::middleware('auth:api')->get('/getuser', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:api']], function (){

    Route::get('/userInfo',function (Request $request) {
        return $request->user();
    });

    Route::post('/authentication', 'API\UserController@authentication');

    Route::get('/user-profile', 'API\UserController@getUser');
    Route::resource('user', 'API\UserController');
    Route::post('/user/{uid}/changepassword', 'API\UserController@changePassword');
    Route::post('/user/{uid}/checkpassword', 'API\UserController@checkPassword');
    Route::get('/filter/user', 'API\UserController@filter');
    Route::get('/userroles', 'API\UserController@userRoles');

    
    Route::resource('tenant', 'API\TenantController');
    Route::get('/filter/tenant', 'API\TenantController@filter');



    
});

