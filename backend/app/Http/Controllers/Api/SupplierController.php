<?php

namespace App\Http\Controllers\Api;

use App\Models\Supplier;
use App\Http\Controllers\BaseApiController;

class SupplierController extends BaseApiController
{
    protected $model = Supplier::class;
    protected $name = 'Supplier';

    protected $storeRules = [
        'name' => 'required|string|max:150',
        'cnpj' => 'nullable|string|max:20',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|string|max:150',
        'status' => 'boolean'
    ];

    protected $updateRules = [
        'name' => 'required|string|max:150',
        'cnpj' => 'nullable|string|max:20',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|string|max:150',
        'status' => 'boolean'
    ];
}
