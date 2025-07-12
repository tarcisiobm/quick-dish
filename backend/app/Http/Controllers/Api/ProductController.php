<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Controllers\BaseApiController;

class ProductController extends BaseApiController
{
    protected string $model = Product::class;
    protected string $name = 'Product';
    protected array $with = ['ingredients'];

    protected array $relations = [
        'ingredients' => [
            'method' => 'sync',
            'fields' => ['quantity']
        ]
    ];
}
