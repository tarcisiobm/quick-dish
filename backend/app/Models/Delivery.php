<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'address_id',
        'price'
    ];


    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
