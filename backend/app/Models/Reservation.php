<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes; // Adicione se for usar soft deletes na tabela users

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes; // Adicione SoftDeletes aqui

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone', // Adicione se não estiver no fillable
        'profile_id', // Adicione se não estiver no fillable
    ];

    // ... o restante do modelo User

    // Relacionamento com reservas (um usuário pode ter muitas reservas)
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class); // Se tiver um modelo Profile
    }
}