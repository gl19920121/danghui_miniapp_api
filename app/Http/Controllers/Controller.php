<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function responseOk(Array $data = [], String $message = '请求成功')
    {
    	return response()->json([
    		'code'    => Response::HTTP_OK,
		    'message' => $message,
		    'data'    => $data
    	])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    protected function responseFail(String $message = '请求失败', Int $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
    	return response()->json([
    		'code'    => $code,
		    'message' => $message
    	])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}
