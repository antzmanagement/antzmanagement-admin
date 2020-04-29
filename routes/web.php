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

use Illuminate\Http\Request;
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('{any}', 'HomeController@index')->where('any', '.*');

Route::post('/upload', function (Request $request) {
    error_log( $request);
    error_log( "file");
    dd($request->file('file'));
});
