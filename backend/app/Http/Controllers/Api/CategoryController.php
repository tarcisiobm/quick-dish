<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryController extends BaseApiController
{
    protected string $model = Category::class;
    protected string $name = 'Category';
    protected ?string $formRequest = CategoryRequest::class;
}
