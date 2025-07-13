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
    'confirmed' => 'The :attribute confirmation does not match.',
    'exists' => 'The selected :attribute is invalid.',

    'custom' => [
        'email' => [
            'unique' => 'The email has already been taken.',
        ],
    ],

    'attributes' => [
        'name' => 'name',
        'email' => 'email',
        'description' => 'description',
        'image' => 'image',
        'display_order' => 'display order',
        'status' => 'status',
        'password' => 'password',
        'phone' => 'phone',
        'current_password' => 'current password',
        'new_password' => 'new password',
        'new_email' => 'new email',
        'token' => 'token',
    ],
];
