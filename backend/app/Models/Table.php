<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory, SoftDeletes; // Inclui SoftDeletes se você adicionou deleted_at na migration

    // Define as colunas que podem ser preenchidas massivamente (para segurança)
    protected $fillable = [
        'number',
        'capacity',
        'status',
    ];

    // Define o relacionamento: uma mesa pode ter muitas reservas
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}