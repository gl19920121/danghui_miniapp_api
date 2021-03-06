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
    Route::get('code2session', 'UsersController@code2Session');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::group(['middleware' => 'can:signed'], function () {
            Route::post('jobs/{job}/collect', 'JobsController@doCollect');
            Route::get('jobs/collects', 'JobsController@listCollect');
        });

        Route::post('sms/send', 'SmsController@send');
        Route::post('sms/verify', 'SmsController@verify');

        Route::post('wechat/encode', 'WechatController@dataEncode');

        Route::post('users/login', 'UsersController@login');
        Route::match(['put', 'patch'], 'users', 'UsersController@update');
        Route::get('users', 'UsersController@show');

        Route::post('jobs/{job}/deliver', 'JobsController@doDeliver');
        Route::get('jobs/deliver', 'JobsController@listDeliver');
        Route::get('jobs/{job}', 'JobsController@show');
        Route::get('jobs', 'JobsController@list');

        Route::post('intentions/store', 'UserIntentionsController@store');
        Route::match(['put', 'patch'], 'intentions/{userIntention}', 'UserIntentionsController@update');
        Route::delete('intentions/{userIntention}', 'UserIntentionsController@destroy');
        Route::get('intentions/{userIntention}', 'UserIntentionsController@show');
        Route::get('intentions', 'UserIntentionsController@list');

        Route::post('resumes/{resume}/work/store', 'ResumesController@workStore');
        Route::delete('resumes/{resume}/work/{resumeWork}', 'ResumesController@workDestroy');
        Route::match(['put', 'patch'], 'resumes/{resume}/work/{resumeWork}', 'ResumesController@workUpdate');
        Route::post('resumes/{resume}/education/store', 'ResumesController@educationStore');
        Route::delete('resumes/{resume}/education/{resumeEdu}', 'ResumesController@educationDestroy');
        Route::match(['put', 'patch'], 'resumes/{resume}/education/{resumeEdu}', 'ResumesController@educationUpdate');
        Route::post('resumes/{resume}/project/store', 'ResumesController@projectStore');
        Route::delete('resumes/{resume}/project/{resumePrj}', 'ResumesController@projectDestroy');
        Route::match(['put', 'patch'], 'resumes/{resume}/project/{resumePrj}', 'ResumesController@projectUpdate');
        Route::post('resumes/upload/avatar', 'ResumesController@uploadAvatar');
        Route::post('resumes/store', 'ResumesController@store');
        Route::post('resumes/send', 'ResumesController@send');
        Route::match(['put', 'patch'], 'resumes/{resume}', 'ResumesController@update');
        Route::delete('resumes/{resume}', 'ResumesController@destroy');
        Route::get('resumes/mine', 'ResumesController@mine');
        Route::get('resumes/{resume}', 'ResumesController@show');
        Route::get('resumes', 'ResumesController@list');

        Route::post('messages/read', 'MessagesController@read');
        Route::match(['put', 'patch'], 'messages/{message}', 'MessagesController@update');
        Route::delete('messages/{message}', 'MessagesController@destroy');
        Route::get('messages', 'MessagesController@list');
    });
});

Route::get('/ws', function () {
    // ???????????????200???????????????
    return;
})->middleware(['auth:api']);
