<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'address_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
