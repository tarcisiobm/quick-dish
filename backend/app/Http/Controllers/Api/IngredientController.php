<?php

namespace App\Http\Controllers\Api;

use App\Models\Ingredient;
use App\Http\Controllers\BaseApiController;

class IngredientController extends BaseApiController
{
    protected $model = Ingredient::class;
    protected $name = 'Ingredient';

    protected $storeRules = [
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

    protected $updateRules = [
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
