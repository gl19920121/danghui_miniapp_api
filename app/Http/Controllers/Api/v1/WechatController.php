<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helper\WechatBizDataCrypt;

class WechatController extends Controller
{
    public function dataEncode(Request $request)
    {
        $appid = config('wechat.app_id');
        $sessionKey = $request->session_key;
        $encryptedData = $request->encrypted_data;
        $iv = $request->iv;

        $pc = new WechatBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);

        if ($errCode !== 0) {
            return $this->responseFail($errCode);
        }

        $data = json_decode($data, true);
        return $this->responseOk($data);
    }
}
