<?php

namespace App\Helpers;

class ApiResponseHelper
{
    public static function success($data = [], $message = null)
    {
        $response = config('api.responses.success');
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => $message ?? $response['message'],
        ], $response['code']);
    }

    public static function created($data = [], $message = null)
    {
        $response = config('api.responses.created');
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => $message ?? $response['message'],
        ], $response['code']);
    }

    public static function unauthorized($message = null)
    {
        $response = config('api.responses.unauthorized');
        return response()->json([
            'status' => 'error',
            'message' => $message ?? $response['message'],
        ], $response['code']);
    }

    public static function unprocessable($message = null)
    {
        $response = config('api.responses.unprocessable');
        return response()->json([
            'status' => 'error',
            'message' => $message ?? $response['message'],
        ], $response['code']);
    }

    public static function error($message = null, $code = null)
    {
        $response = config('api.responses.error');
        return response()->json([
            'status' => 'error',
            'message' => $message ?? $response['message'],
        ], $code ?? $response['code']);
    }

    public static function notFound($message = null)
    {
        $response = config('api.responses.not_found');
        return response()->json([
            'status' => 'error',
            'message' => $message ?? $response['message'],
        ], $response['code']);
    }

    public static function serverError($message = null)
    {
        $response = config('api.responses.server_error');
        return response()->json([
            'status' => 'error',
            'message' => $message ?? $response['message'],
        ], $response['code']);
    }

    public static function forbidden($message = null)
    {
        $response = config('api.responses.forbidden');
        return response()->json([
            'status' => 'error',
            'message' => $message ?? $response['message'],
        ], $response['code']);
    }
}
