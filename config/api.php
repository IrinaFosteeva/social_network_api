<?php

return [
    'responses' => [
        'success' => [
            'code' => 200,
            'message' => 'Request was successful',
        ],
        'created' => [
            'code' => 201,
            'message' => 'Resource created successfully',
        ],
        'unauthorized' => [
            'code' => 401,
            'message' => 'Unauthorized access',
        ],
        'forbidden' => [
            'code' => 403,
            'message' => 'Forbidden',
        ],
        'unprocessable' => [
            'code' => 422,
            'message' => 'Unprocessable entity',
        ],
        'error' => [
            'code' => 400,
            'message' => 'An error occurred',
        ],
        'not_found' => [
            'code' => 404,
            'message' => 'Resource not found',
        ],
        'server_error' => [
            'code' => 500,
            'message' => 'Internal server error',
        ],
    ],
];
