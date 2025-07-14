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
    'numeric' => 'The :attribute must be a number.',
    'array' => 'The :attribute must be an array.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
    ],

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
        'category_id' => 'category',
        'price' => 'price',
        'promotional_price' => 'promotional price',
        'ingredients' => 'ingredients',
        'ingredients.*.id' => 'ingredient',
        'ingredients.*.quantity' => 'quantity',
        'number' => 'number',
        'capacity' => 'capacity',
        'zipcode' => 'zip code',
        'street' => 'street',
        'city' => 'city',
        'state' => 'state',
        'refference' => 'refference',
        'complement' => 'complement',
        'is_default' => 'default value',
        'address_id' => 'address'
    ],
];
