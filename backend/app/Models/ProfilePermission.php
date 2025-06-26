<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfilePermission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'profile_permissions';

    protected $fillable = [
        'profile_id',
        'permission_id',
    ];


    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}