<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class UsersController extends Controller
{
    public function code2Session(Request $request)
    {
    	$response = Http::get(config('wechat.code_2_session_url'), [
    		'appid' => config('wechat.app_id'),
    		'secret' => config('wechat.app_secret'),
    		'js_code' => $request->code,
    		'grant_type' => config('wechat.grant_type'),
    	]);
    	$res = $response->json();

    	return $this->responseOk();
    }
}
