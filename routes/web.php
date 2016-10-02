<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

//API
Route::group(['prefix' => 'api', 'namespace' => 'Api'], function ($router) {
    //用户API
    Route::group(['prefix' => 'user'], function ($router) {
        $router->post('/room/apply','UserInfoApiController@applyLiveRoom');
        $router->get('/publish','UserInfoApiController@getPublishUrl');
    });
    //开放API
    $router->get('/stream/play/{id}','StreamApiController@getPlayUrl');
});