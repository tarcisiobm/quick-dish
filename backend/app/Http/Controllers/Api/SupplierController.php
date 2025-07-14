<?php

namespace App\Http\Controllers\Api;

use App\Models\Supplier;
use App\Http\Controllers\ApiController;
use App\Http\Requests\SuppllierRequest;

class SupplierController extends ApiController
{
    protected string $model = Supplier::class;
    protected string $name = 'Supplier';
    protected ?string $formRequest = SuppllierRequest::class;
}
