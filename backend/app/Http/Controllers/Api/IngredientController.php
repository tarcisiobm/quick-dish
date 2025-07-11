<?php

namespace App\Http\Controllers\Api;

use App\Models\Ingredient;
use App\Http\Controllers\BaseApiController;

class IngredientController extends BaseApiController
{
    protected string $model = Ingredient::class;
    protected string $name = 'Ingredient';
    protected array $with = ['supplier', 'unitMeasure'];

    protected array $storeRules = [
        'name' => 'required|string|max:150',
        'description' => 'sometimes|nullable|string',
        'unit_price' => 'required|numeric|min:0',
        'quantity' => 'required|numeric|min:0',
        'min_quantity' => 'required|numeric|min:0',
        'max_quantity' => 'sometimes|nullable|numeric|min:0',
        'supplier_id' => 'required|exists:suppliers,id',
        'unit_measure_id' => 'required|exists:unit_measures,id',
        'is_additional' => 'sometimes|boolean',
        'status' => 'boolean'
    ];

    protected array $updateRules = [
        'name' => 'required|string|max:150',
        'description' => 'string',
        'unit_price' => 'required|numeric|min:0',
        'quantity' => 'required|numeric|min:0',
        'min_quantity' => 'required|numeric|min:0',
        'max_quantity' => 'numeric|min:0',
        'supplier_id' => 'required|exists:suppliers,id',
        'unit_measure_id' => 'required|exists:unit_measures,id',
        'is_additional' => 'boolean',
        'status' => 'boolean'
    ];
}
