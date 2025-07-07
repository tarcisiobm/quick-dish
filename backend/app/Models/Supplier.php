<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "name",
        "cnpj",
        "phone",
        "email",
        "status"
    ];

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
}
