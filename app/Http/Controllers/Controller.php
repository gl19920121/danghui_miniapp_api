<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Response;
use App\Helper\ApiResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function responseOk(Array $data = [], String $message = '', Int $code = ApiResponse::API_OK)
    {
        $apiResponse = ApiResponse::create($code);
        if (empty($message)) {
            $message = $apiResponse->getStatusText();
        }

    	return response()->json([
    		'code'    => ApiResponse::API_OK,
		    'message' => $message,
		    'data'    => $data
    	], Response::HTTP_OK)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    protected function responseFail(Int $code = ApiResponse::API_FAIL, String $message = '')
    {
        $apiResponse = ApiResponse::create($code);
        if (empty($message)) {
            $message = $apiResponse->getStatusText();
        }

    	return response()->json([
    		'code'    => $code,
		    'message' => $message
    	], Response::HTTP_OK)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}
