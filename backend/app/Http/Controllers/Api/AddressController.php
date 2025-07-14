<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Http\Requests\AddressRequest;
use App\Models\Address;

class AddressController extends ApiController
{
    protected string $model = Address::class;
    protected string $name = 'Address';
    protected ?string $formRequest =  AddressRequest::class;
}
