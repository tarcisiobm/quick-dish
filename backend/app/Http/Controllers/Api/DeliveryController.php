<?php

namespace App\Http\Controllers\Api;

use App\Models\Delivery;
use App\Http\Requests\DeliveryRequest;
use App\Http\Controllers\ApiController;

class DeliveryController extends ApiController
{
    protected string $model = Delivery::class;
    protected string $name = 'Delivery';
    protected ?string $formRequest = DeliveryRequest::class;
}
