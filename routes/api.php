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

    Route::resource('staff', 'API\StaffController');
    Route::get('/filter/staff', 'API\StaffController@filter');

    Route::resource('owner', 'API\OwnerController');
    Route::get('/filter/owner', 'API\OwnerController@filter');

    Route::resource('room', 'API\RoomController');
    Route::get('/filter/room', 'API\RoomController@filter');

    Route::resource('roomtype', 'API\RoomTypeController');
    Route::get('/filter/roomtype', 'API\RoomTypeController@filter');
    
    
    Route::resource('property', 'API\PropertyController');
    Route::get('/filter/property', 'API\PropertyController@filter');

    Route::resource('maintenance', 'API\MaintenanceController');
    Route::get('/filter/maintenance', 'API\MaintenanceController@filter');

    Route::resource('contract', 'API\ContractController');
    Route::get('/filter/contract', 'API\ContractController@filter');

    Route::resource('rentalpayment', 'API\RentalPaymentController');
    Route::get('/filter/rentalpayment', 'API\RentalPaymentController@filter');
    Route::post('/rentalpayment/{uid}/makepayment', 'API\RentalPaymentController@makePayment');

});


Route::get('/public/roomtype', 'API\RoomTypeController@getPublicRoomTypes');

