<?php

namespace App\Traits\Response;

use Illuminate\Http\JsonResponse;

trait HasApiResponse
{
    public function send($data,$message = '' , $statusCode = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $statusCode < 400,
        ], $statusCode);
    }
}
