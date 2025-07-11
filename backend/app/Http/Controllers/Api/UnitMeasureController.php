<?php

namespace App\Http\Controllers\Api;

use App\Models\UnitMeasure;
use App\Http\Controllers\BaseApiController;

class UnitMeasureController extends BaseApiController
{
    protected $model = UnitMeasure::class;
    protected $name = 'Unit measure';

    protected $storeRules = [
        'name' => 'required|string|max:150',
        'abbreviation' => 'nullable|string|max:15'
    ];

    protected $updateRules = [
        'name' => 'required|string|max:150',
        'abbreviation' => 'nullable|string|max:15'
    ];
}
