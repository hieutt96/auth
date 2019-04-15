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

	Route::get('/user/list', 'UserController@getList')->middleware('auth:api')->name('user.list');

	Route::get('/user/detail', 'UserController@detail')->middleware('auth:api')->name('user.detail');
});	
