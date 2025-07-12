<?php

return [
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute must be a string.',
    'max' => [
        'string' => 'The :attribute may not be greater than :max characters.',
    ],
    'min' => [
        'string' => 'The :attribute must be at least :min characters.',
        'numeric' => 'The :attribute must be at least :min.',
    ],
    'email' => 'The :attribute must be a valid email address.',
    'unique' => 'The :attribute has already been taken.',
    'integer' => 'The :attribute must be an integer.',
    'image' => 'The :attribute must be an image.',
    'boolean' => 'The :attribute field must be true or false.',

    'attributes' => [
        'name' => 'name',
        'email' => 'email',
        'description' => 'description',
        'image' => 'image',
        'display_order' => 'display order',
        'status' => 'status',
        'password' => 'password',
    ],
];
