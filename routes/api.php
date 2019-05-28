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

Route::group(['prefix' => 'user'], function() {

	Route::post('/register', 'UserController@postRegister')->name('user.post.register');

	Route::post('/login', 'UserController@postLogin')->name('user.post.login');

	Route::get('/list', 'UserController@getList')->middleware('auth:api')->name('user.list');

	Route::get('/detail', 'UserController@detail')->middleware('auth:api')->name('user.detail');

	Route::post('/active', 'UserController@active')->name('user.active');

	Route::post('/create-google2fa-secret', 'UserController@createGoogle2fa')->name('user.create.google2fa')->middleware('auth:api');

	Route::post('/off-google2fa', 'UserController@offGoogle2fa')->name('user.off.google2fa')->middleware('auth:api');

	Route::get('/detail-google2fa', 'UserController@detailGoogle2fa')->name('user.detail.google2fa')->middleware('auth:api');
	Route::post('/edit', 'UserController@edit')->name('user.edit')->middleware('auth:api');
});	

Route::post('/check-user-exists', 'UserController@checkExists')->name('user.check.exists');

Route::post('/check-password', 'UserController@checkPassword')->name('user.check.password');

Route::get('/txn-list-notification', 'TxnController@list')->name('txn.list')->middleware('auth:api');

Route::get('/txn-detail', 'TxnController@detail')->name('txn.detail')->middleware('auth:api');