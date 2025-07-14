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
    'confirmed' => 'A confirmação do campo :attribute não confere.',
    'exists' => 'O :attribute selecionado é inválido.',
    'numeric' => 'O campo :attribute deve ser um número.',
    'array' => 'O campo :attribute deve ser um array.',
    'required_with' => 'O campo :attribute é obrigatório quando :values está presente.',
    'lt' => [
        'numeric' => 'O campo :attribute deve ser menor que :value.',
    ],

    'custom' => [
        'email' => [
            'unique' => 'Este e-mail já está em uso.',
        ],
    ],

    'attributes' => [
        'name' => 'nome',
        'email' => 'e-mail',
        'description' => 'descrição',
        'image' => 'imagem',
        'display_order' => 'ordem de exibição',
        'status' => 'status',
        'password' => 'senha',
        'phone' => 'telefone',
        'current_password' => 'senha atual',
        'new_password' => 'nova senha',
        'new_email' => 'novo e-mail',
        'token' => 'token',
        'category_id' => 'categoria',
        'price' => 'preço',
        'promotional_price' => 'preço promocional',
        'ingredients' => 'ingredientes',
        'ingredients.*.id' => 'ingrediente',
        'ingredients.*.quantity' => 'quantidade',
        'number' => 'número',
        'capacity' => 'capacidade',
        'zipcode' => 'CEP',
        'street' => 'rua',
        'city' => 'cidade',
        'state' => 'estado',
        'refference' => 'referência',
        'complement' => 'complemento',
        'is_default' => 'valor padrão',
    ],
];
