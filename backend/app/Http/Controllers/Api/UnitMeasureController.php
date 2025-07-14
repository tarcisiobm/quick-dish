<?php

namespace App\Http\Controllers\Api;

use App\Models\UnitMeasure;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\UnitMeasureRequest;

class UnitMeasureController extends BaseApiController
{
    protected string $model = UnitMeasure::class;
    protected string $name = 'UnitMeasure';
    protected ?string $formRequest = UnitMeasureRequest::class;
}
