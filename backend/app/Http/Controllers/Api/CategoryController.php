<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\BaseApiController;
use App\Models\Category;

class CategoryController extends BaseApiController
{
    protected string $model = Category::class;
    protected string $name = 'Category';

    protected array $storeRules = [
        'name' => 'required|string|max:150',
        'description' => 'sometimes|nullable|string|max:255',
        'image' => 'sometimes|nullable|image|max:2048',
        'display_order' => 'sometimes|nullable|integer|min:1'
    ];

    protected array $updateRules = [
        'name' => 'sometimes|string|max:150',
        'description' => 'sometimes|nullable|string|max:255',
        'image' => 'sometimes|nullable|image|max:2048',
        'display_order' => 'sometimes|nullable|integer|min:1',
        'status' => 'sometimes|boolean'
    ];
}
