<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Helper\ApiResponse;

class SmsController extends Controller
{
    public function send(Request $request)
    {
        // 根据模板变量进行内容填充
        $json_param = [];
        $json_param["code"] = rand(0000, 9999);
        $params = array(
            "templateId" => $request->template_id,
            "mobile" => $request->mobile,
            "paramType" => "json",
            // 转换成json字符串
            "params" => json_encode($json_param),
            // 国际短信对应的国际编码(非国际短信接入请注释掉该行代码)
            // "internationalCode" => "对应的国家编码",
            "codeLen" => "4",
            "codeName" => "code",
            "codeValidSec" => "600",
        );

        $res = $this->check($params, config('sms.api_url.send'));
        // return $this->responseOk($res['data']);
        if ($res['code'] !== 200) {
            switch ($res['code']) {
                case 506:
                    return $this->responseFail(ApiResponse::SMS_SEND_EXCEED_PHONE_SEND_LIMIT);
                    break;

                default:
                    return $this->responseFail(ApiResponse::SMS_SEND_FAIL);
                    break;
            }
        }

        return $this->responseOk($res['data']);
    }

    public function verify(Request $request)
    {
        $params = array(
            "requestId" => $request->request_id,
            "code" => $request->code
        );

        $res = $this->check($params, config('sms.api_url.check'));
        return $this->responseOk($res);
        if ($res['data']['match'] !== true) {
            switch ($res['data']['reasonType']) {
                case 2:
                    return $this->responseFail(ApiResponse::SMS_CHECK_FAIL);
                    break;
                case 3:
                    return $this->responseFail(ApiResponse::SMS_CHECK_OVERDUE);
                    break;

                default:
                    return $this->responseFail();
                    break;
            }
        }

        return $this->responseOk();
    }

    private function check($params, $url)
    {
        $params["secretId"] = config('sms.secret_id');
        $params["businessId"] = config('sms.business_id');
        $params["version"] = config('sms.version');
        $params["timestamp"] = sprintf("%d", round(microtime(true) * 1000)); // time in milliseconds
        $params["nonce"] = sprintf("%d", rand()); // random int
        $params = $this->toUtf8($params);
        $params["signature"] = $this->gen_signature(config('sms.secret_key'), $params);

        $response = Http::withHeaders([
            'Content-type' => "application/x-www-form-urlencoded"
        ])
        ->timeout(config('sms.api_timeout'))
        ->get($url, $params);

        $res = $response->json();
        return $res;
    }

    /**
     * 计算参数签名
     * $params 请求参数
     * $secretKey secretKey
     */
    private function gen_signature($secretKey, $params)
    {
        ksort($params);
        $buff = "";
        foreach($params as $key => $value) {
            // if ($value !== null) {
            // }
            $buff .= $key;
            $buff .= $value;
        }
        $buff .= $secretKey;
        // return $buff;
        return md5(mb_convert_encoding($buff, "utf8", "auto"));
    }

    /**
     * 将输入数据的编码统一转换成utf8
     * @params 输入的参数
     */
    function toUtf8($params)
    {
        $utf8s = array();
        foreach ($params as $key => $value) {
            $utf8s[$key] = is_string($value) ? mb_convert_encoding($value, "utf8", config('sms.internal_string_charset')) : $value;
        }
        return $utf8s;
    }
}
