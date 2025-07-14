<?php

namespace App\Http\Controllers\Api;

use App\Models\Ingredient;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\IngredientRequest;

class IngredientController extends BaseApiController
{
    protected string $model = Ingredient::class;
    protected string $name = 'Ingredient';
    protected ?string $formRequest = IngredientRequest::class;
    protected array $with = ['supplier', 'unitMeasure'];
}
