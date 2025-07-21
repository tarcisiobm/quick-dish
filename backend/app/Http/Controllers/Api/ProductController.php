<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends ApiController
{
    protected string $model = Product::class;
    protected string $name = 'Product';
    protected array $with = ['ingredients', 'category'];
    protected ?string $formRequest = ProductRequest::class;

    protected array $relations = [
        'ingredients' => [
            'method' => 'sync',
            'fields' => ['quantity']
        ]
    ];


    public function index(Request $request): JsonResponse
    {
        $query = $this->model::query();

        if ($this->with) {
            $query->with($this->with);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        return $this->successResponse(null, $query->get());
    }
}
