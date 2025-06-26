<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'profiles';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    // Relacionamentos
    public function users()
    {
        return $this->hasMany(User::class); 
    }

    public function profilePermissions()
    {
        return $this->hasMany(ProfilePermission::class); 
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'profile_permissions'); 
    }
}