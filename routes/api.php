<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\JobsController;

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

Route::group(['namespace' => 'Api'], function () {
    Route::get('/code2session', [UsersController::class, 'code2Session']);
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/user/login', [UsersController::class, 'login']);
        Route::get('/user', [UsersController::class, 'show']);
        Route::get('/job', [JobsController::class, 'list']);
        Route::get('/job/{job}', [JobsController::class, 'show']);

        Route::group(['middleware' => 'can:signed'], function () {
            Route::post('/job/{job}/collect', [JobsController::class, 'collect']);
        });
    });
});
