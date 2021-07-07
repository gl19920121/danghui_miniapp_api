<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function responseOk(String $message = '请求成功', Array $data = [])
    {
    	return response()->json([
    		'code'    => Response::HTTP_OK,
		    'message' => $message,
		    'data'    => $data
    	]);
    }
}
