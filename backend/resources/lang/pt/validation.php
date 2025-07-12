<?php

return [
    'required' => 'O campo :attribute é obrigatório.',
    'string' => 'O campo :attribute deve ser um texto.',
    'max' => [
        'string' => 'O campo :attribute não pode ter mais que :max caracteres.',
    ],
    'min' => [
        'string' => 'O campo :attribute deve ter no mínimo :min caracteres.',
        'numeric' => 'O campo :attribute deve ser no mínimo :min.',
    ],
    'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'unique' => 'O campo :attribute já está em uso.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'image' => 'O campo :attribute deve ser uma imagem.',
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',

    'attributes' => [
        'name' => 'nome',
        'email' => 'e-mail',
        'description' => 'descrição',
        'image' => 'imagem',
        'display_order' => 'ordem de exibição',
        'status' => 'status',
        'password' => 'senha',
    ],
];
