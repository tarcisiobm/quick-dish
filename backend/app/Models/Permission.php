<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'permissions';

    protected $fillable = [
        'permission_group_id',
        'name',
        'description',
    ];

    public function permissionGroup()
    {
        return $this->belongsTo(PermissionGroup::class); 
    }

    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'profile_permissions'); 
    }
}