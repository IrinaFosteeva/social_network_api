<?php


return [
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute must be a string.',
    'min' => [
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'regex' => [
        'letters' => 'The :attribute must contain at least one letter.',
        'numbers' => 'The :attribute must contain at least one number.',
        'special' => 'The :attribute must contain at least one special character.',
    ],
];
