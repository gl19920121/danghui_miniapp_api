<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Helper\ApiResponse;
use Auth;

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

        if (isset($res['errcode']) && $res['errcode'] !== 0) {
            switch ($res['errcode']) {
                case -1:
                    $code = ApiResponse::WECHAT_SYSTEM_BUSY;
                    break;
                case 40029:
                    $code = ApiResponse::WECHAT_CODE_INVALID;
                    break;
                case 40163:
                    $code = ApiResponse::WECHAT_CODE_BE_USED;
                    break;
                case 41008:
                    $code = ApiResponse::WECHAT_CODE_MISS;
                    break;
                case 45011:
                    $code = ApiResponse::WECHAT_FREQUENCY_TOO_HIGH;
                    break;

                default:
                    $code = ApiResponse::API_FAIL;
                    break;
            }

            return $this->responseFail($code);
        }

        $openid = $res['openid'];
        $sessionKey = $res['session_key'];
        // $openid = 'oKfCB5awT_r4T2arSw-efI8gBh3I';
        // $sessionKey = 'dwTvK82st1hZIgm8slL7sw==';

        $user = User::openid($openid)->first();
        if ( ! $user ) {
            $user = User::create([
                'openid' => $openid,
                'session_key' => $sessionKey,
                'password' => bcrypt($openid),
            ]);
        }

        $data = [
            'openid' => $openid,
            'session_key' => $sessionKey,
            'user' => $user->toArray(),
        ];
        return $this->responseOk($data);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required',
        ]);

        $user = $request->user();
        $openid = $user->openid;
        $sessionKey = $user->session_key;
        $phone = $request->phone;

        if ( ! $user->is_register ) {
            $request->user()->update([
                'phone' => $phone,
            ]);
        }

        $data = Auth::user()->toArray();
        return $this->responseOk($data);
    }

    public function show(Request $request)
    {
        $data = $request->user()->toArray();
        return $this->responseOk($data);
    }

    public function update(Request $request)
    {
        $request->user()->update($request->all());

        return $this->responseOk();
    }
}
