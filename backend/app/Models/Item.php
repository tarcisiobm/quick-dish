<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'promotional_price',
        'image_path',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function additionals()
    {
        return $this->belongsToMany(Additional::class, 'item_additionals');
    }
}
