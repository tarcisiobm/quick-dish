<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\BaseApiController;
use App\Models\Category;

class CategoryController extends BaseApiController
{
    protected $model = Category::class;
    protected $name = 'Category';

    protected $storeRules = [
        'name' => 'required|string|max:150',
        'description' => 'nullable|string|max:255',
        'image' => 'nullable|image|max:2048',
        'display_order' => 'nullable|integer|min:1'
    ];

    protected $updateRules = [
        'name' => 'nullable|string|max:150',
        'description' => 'nullable|string|max:255',
        'image' => 'nullable|image|max:2048',
        'display_order' => 'nullable|integer|min:1',
        'status' => 'boolean'
    ];
}
