<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'description',
        'discout_type',
        'discount_value',
        'min_order_value',
        'usage_limit',
        'start_date',
        'end_date',
        'status'
    ];
}
