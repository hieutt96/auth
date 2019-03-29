<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Exceptions\AppException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function responseJson($data, $count = 1) {
        return response()->json([
            'code'=>AppException::ERR_NONE,
            'message'=>[],
            'count'=>$count,
            'data'=>$data,
        ]);
    }
}
