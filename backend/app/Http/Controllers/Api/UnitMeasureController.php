<?php

namespace App\Http\Controllers\Api;

use App\Models\UnitMeasure;
use App\Http\Controllers\BaseApiController;

class UnitMeasureController extends BaseApiController
{
    protected string $model = UnitMeasure::class;
    protected string $name = 'UnitMeasure';

    protected array $storeRules = [
        'name' => 'required|string|max:150',
        'abbreviation' => 'nullable|string|max:15'
    ];

    protected array $updateRules = [
        'name' => 'required|string|max:150',
        'abbreviation' => 'nullable|string|max:15'
    ];
}
