<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitMeasure extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "name",
        "abbreviation"
    ];

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
}
