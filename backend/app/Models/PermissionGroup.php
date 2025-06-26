<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'permission_groups';

    protected $fillable = [
        'name',
        'description',
    ];

    // Relacionamentos (adicionaremos aqui à medida que criarmos os outros models)
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}