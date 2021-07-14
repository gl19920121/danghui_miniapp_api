<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['namespace' => 'App\Http\Controllers\Api\v1', 'prefix' => 'api/v1'], function () {
    Route::get('/code2session', 'UsersController@code2Session');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/user/login', 'UsersController@login');
        Route::get('/user', 'UsersController@show');
        Route::get('/job', 'JobsController@list');
        Route::get('/job/{job}', 'show@JobsController');

        Route::group(['middleware' => 'can:signed'], function () {
            Route::get('/job/collect', 'JobsController@listCollect');
            Route::post('/job/{job}/collect', 'JobsController@doCollect');
        });
    });
});
