<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class UsersController extends Controller
{
    public function code2Session(Request $request)
    {
    // 	$response = Http::get(config('wechat.code_2_session_url'), [
    // 		'appid' => config('wechat.app_id'),
    // 		'secret' => config('wechat.app_secret'),
    // 		'js_code' => $request->code,
    // 		'grant_type' => config('wechat.grant_type'),
    // 	]);
    // 	$res = $response->json();

    // 	if (isset($res['errcode']) && $res['errcode'] !== 0) {
    // 		switch ($res['errcode']) {
	   //  		case -1:
	   //  			$msg = '系统繁忙，请稍后再试';
	   //  			break;
	   //  		case 40029:
	   //  			$msg = 'code无效';
	   //  			break;
				// case 45011:
	   //  			$msg = '操作频率过高，请稍后尝试';
	   //  			break;
    // 			case 40163:
    // 				$msg = 'code已被使用';
    // 				break;
	    		
	   //  		default:
	   //  			$msg = '';
	   //  			break;
	   //  	}

	   //  	return $this->responseFail($msg);
    // 	}

    	// $openid = $res['openid'];
    	// $sessionKey = $res['session_key'];
    	$openid = 'oKfCB5awT_r4T2arSw-efI8gBh3I';
    	$user = User::openid($openid)->first();
    	if ($user) {
    		$data = $user;
    	} else {
    		$data = User::create([
    			'openid' => $openid,
    		]);
    	}

    	return $this->responseOk($data);
    }
}
