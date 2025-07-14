<?php

namespace App\Http\Controllers\Api;

use App\Models\Ingredient;
use App\Http\Controllers\ApiController;
use App\Http\Requests\IngredientRequest;

class IngredientController extends ApiController
{
    protected string $model = Ingredient::class;
    protected string $name = 'Ingredient';
    protected ?string $formRequest = IngredientRequest::class;
    protected array $with = ['supplier', 'unitMeasure'];
}
