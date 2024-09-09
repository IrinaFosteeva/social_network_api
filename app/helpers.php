<?php

use App\Helpers\ApiResponseHelper;

if (!function_exists('api_success')) {
    function api_success($data = [], $message = 'Request was successful')
    {
        return ApiResponseHelper::success($data, $message);
    }
}

if (!function_exists('api_error')) {
    function api_error($message = 'An error occurred', $code = 400)
    {
        return ApiResponseHelper::error($message, $code);
    }
}
