<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected function errorResponse($message, $errors=null, $code=422) {
        if($message == null && is_string($errors))
            $message = $errors;
        return response()->json([
            'errors' => $errors,
            'data' => null,
            'message' => $message,
            'status' => 'error'
        ], $code);
    }

    protected function successResponse($message, $data=null, $code=200) {
        return response()->json([
            'errors' => null,
            'data' => $data,
            'message' => $message,
            'status' => 'success'
        ], $code);
    }
}
