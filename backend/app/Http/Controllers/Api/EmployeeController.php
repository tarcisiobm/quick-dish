<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Http\Controllers\ApiController;
use App\Http\Requests\EmployeeRequest;


class EmployeeController extends ApiController
{
    protected string $model = Employee::class;
    protected string $name = 'Employee';
    protected ?string $formRequest = EmployeeRequest::class;

    protected array $with = ['user'];
}
