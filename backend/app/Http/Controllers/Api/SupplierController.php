<?php

namespace App\Http\Controllers\Api;

use App\Models\Supplier;
use App\Http\Controllers\BaseApiController;

class SupplierController extends BaseApiController
{
    protected string $model = Supplier::class;
    protected string $name = 'Supplier';

    protected array $storeRules = [
        'name' => 'required|string|max:150',
        'cnpj' => 'sometimes|nullable|string|max:20',
        'phone' => 'sometimes|nullable|string|max:20',
        'email' => 'sometimes|nullable|string|max:150',
        'status' => 'sometimes|boolean'
    ];

    protected array $updateRules = [
        'name' => 'sometimes|string|max:150',
        'cnpj' => 'sometimes|nullable|string|max:20',
        'phone' => 'sometimes|nullable|string|max:20',
        'email' => 'sometimes|nullable|string|max:150',
        'status' => 'sometimes|boolean'
    ];
}
