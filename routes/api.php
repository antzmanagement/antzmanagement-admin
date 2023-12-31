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


Route::group(['middleware' => ['auth:api']], function (){

    Route::get('/authenticate', 'API\UserController@authentication');

    Route::get('/user-profile', 'API\UserController@getUser');
    Route::resource('user', 'API\UserController');
    Route::post('/user/{uid}/changepassword', 'API\UserController@changePassword');
    Route::post('/user/{uid}/checkpassword', 'API\UserController@checkPassword');
    Route::get('/filter/user', 'API\UserController@filter');
    Route::get('/userroles', 'API\UserController@userRoles');

    
    
    Route::resource('report', 'API\ReportController');
    Route::get('/filter/report', 'API\ReportController@filter');

    Route::resource('role', 'API\RoleController');
    Route::get('/filter/role', 'API\RoleController@filter');

    Route::resource('tenant', 'API\TenantController');
    Route::get('/filter/tenant', 'API\TenantController@filter');

    Route::resource('staff', 'API\StaffController');
    Route::get('/filter/staff', 'API\StaffController@filter');

    Route::resource('owner', 'API\OwnerController');
    Route::get('/owner/{uid}/getUnclaimRentalPayments', 'API\OwnerController@getUnclaimRentalPayments');
    Route::get('/owner/{uid}/getUnclaimMaintenances', 'API\OwnerController@getUnclaimMaintenances');
    Route::get('/filter/owner', 'API\OwnerController@filter');

    Route::resource('room', 'API\RoomController');
    Route::get('/filter/room', 'API\RoomController@filter');

    Route::resource('roomtype', 'API\RoomTypeController');
    Route::get('/filter/roomtype', 'API\RoomTypeController@filter');
    
    
    Route::resource('property', 'API\PropertyController');
    Route::get('/filter/property', 'API\PropertyController@filter');

    Route::resource('service', 'API\ServiceController');
    Route::get('/filter/service', 'API\ServiceController@filter');

    Route::resource('maintenance', 'API\MaintenanceController');
    Route::get('/filter/maintenance', 'API\MaintenanceController@filter');

    Route::resource('cleaning', 'API\CleaningController');
    Route::get('/filter/cleaning', 'API\CleaningController@filter');

    Route::resource('roomcheck', 'API\RoomCheckController');
    Route::get('/filter/roomcheck', 'API\RoomCheckController@filter');

    Route::resource('contract', 'API\ContractController');
    Route::get('/filter/contract', 'API\ContractController@filter');

    Route::resource('rentalpayment', 'API\RentalPaymentController');
    Route::get('/filter/rentalpayment', 'API\RentalPaymentController@filter');
    Route::post('/rentalpayment/{uid}/makepayment', 'API\RentalPaymentController@makePayment');

    Route::resource('payment', 'API\PaymentController');
    Route::get('/filter/payment', 'API\PaymentController@filter');
    Route::post('/payment/{uid}/makepayment', 'API\PaymentController@makePayment');
    Route::post('/paydeposit', 'API\PaymentController@payDeposit');

    Route::resource('claim', 'API\ClaimController');
    Route::get('/filter/claim', 'API\ClaimController@filter');

    Route::resource('roomcontract', 'API\RoomContractController');
    Route::get('/filter/roomcontract', 'API\RoomContractController@filter');
    Route::post('/transfer/roomcontract', 'API\RoomContractController@transfer');
    Route::post('/checkout/roomcontract', 'API\RoomContractController@checkout');

});


Route::get('/public/roomtype', 'API\RoomTypeController@getPublicRoomTypes');

