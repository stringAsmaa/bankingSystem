<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    static function sendResponse($code = 200, $msg = null, $data = [])
    {
        $response = [
            'status' => $code,
            'message' => $msg,
            'data' => $data
        ];
        return response()->json($response, $code);
    }

    public static function sendError($message, $status = 400, $details = [])
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];
        return response()->json($response, $status);
    }
}
