<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Table;
use App\Http\Requests\TableRequest;

class TableController extends ApiController
{
    protected string $model = Table::class;
    protected string $name = 'Table';
    protected ?string $formRequest = TableRequest::class;
}
