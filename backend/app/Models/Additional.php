<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Additional extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'status'
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_additionals');
    }
}
