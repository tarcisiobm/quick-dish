<?php

namespace App\Http\Controllers\Api;

use App\Models\PaymentType;
use App\Http\Requests\PaymentTypeRequest;
use App\Http\Controllers\ApiController;

class PaymentTypeController extends ApiController
{
    protected string $model = PaymentType::class;
    protected string $name = 'PaymentType';
    protected ?string $formRequest = PaymentTypeRequest::class;
}
