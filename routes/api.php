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

Route::post('/login', 'LoginController@login')->name('api.login');

Route::group(['middleware' => 'auth:api'], function(){
    Route::get('/bills/{login}', 'AccountController@getBills')->name('api.bills');
    Route::get('/cards/{id_bill}', 'CardController@getCards')->name('api.cards');
    Route::get('/card_detail/{id_card}', 'CardController@getCard')->name('api.card_detail');
    Route::get('/dictionary/{name}', 'DictionaryController@getDictionary')->name('api.dictionary');
});
