<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\Coupon;
use App\Http\Requests\CouponRequest;

class CouponController extends ApiController
{
    protected string $model = Coupon::class;
    protected string $name = 'Coupon';
    protected ?string $formRequest = CouponRequest::class;
}
