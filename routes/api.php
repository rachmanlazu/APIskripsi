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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
*/

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
// Route::get('details', 'API\UserController@details');
Route::group(['middleware' => 'auth:api'], function(){
    Route::get('pasien/getDetail', 'API\UserController@getDetail')->name('pasien.getDetail');
    Route::patch('pasien/setDetail', 'API\UserController@setDetail')->name('pasien.setDetail');

    Route::post('pasien/ambilAntrian', 'API\ReservasiController@ambilAntrian')->name('pasien.ambilAntrian');

});
