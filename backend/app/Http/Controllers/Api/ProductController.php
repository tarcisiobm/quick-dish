<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Controllers\BaseApiController;

class ProductController extends BaseApiController
{
    protected string $model = Product::class;
    protected string $name = 'Product';
    protected array $with = ['ingredients'];

    protected array $storeRules = [
        'name' => 'required|string|max:150',
        'price' => 'required|numeric|min:0',
        'promotional_price' => 'nullable|numeric|min:0|lte:price',
        'description' => 'nullable|string|max:1000',
        'image_path' => 'nullable|string|max:255',
        'status' => 'boolean',
        'category_id' => 'required|exists:categories,id',

        'ingredients' => 'required|array|min:1',
        'ingredients.*.id' => 'required|exists:ingredients,id',
        'ingredients.*.quantity' => 'numeric|min:0',
    ];

    protected array $updateRules = [
        'name' => 'sometimes|string|max:150',
        'price' => 'sometimes|numeric|min:0',
        'promotional_price' => 'nullable|numeric|min:0|lte:price',
        'description' => 'nullable|string|max:1000',
        'image_path' => 'sometimes|string|max:255',
        'status' => 'boolean',
        'category_id' => 'sometimes|exists:categories,id',

        'ingredients' => 'sometimes|array|min:1',
        'ingredients.*.id' => 'required|exists:ingredients,id',
        'ingredients.*.quantity' => 'required|numeric|min:0',
    ];


    protected array $relations = [
        'ingredients' => [
            'method' => 'sync',
            'fields' => ['quantity']
        ]
    ];
}
