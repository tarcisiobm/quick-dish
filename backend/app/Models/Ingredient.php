<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "name",
        "description",
        "unit_price",
        "quantity",
        "min_quantity",
        "max_quantity",
        "is_additional",
        "status"
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function unitMeasure(){
        return $this->belongsTo(UnitMeasure::class);
    }
}

