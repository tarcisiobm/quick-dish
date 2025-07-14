<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
=======
>>>>>>> 71a0c8232b9d8407a9e1c0f3ae088b5661282041
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
<<<<<<< HEAD
    use HasFactory, SoftDeletes; // Inclui SoftDeletes se você adicionou deleted_at na migration

    // Define as colunas que podem ser preenchidas massivamente (para segurança)
=======
    use SoftDeletes;

>>>>>>> 71a0c8232b9d8407a9e1c0f3ae088b5661282041
    protected $fillable = [
        'number',
        'capacity',
        'status',
    ];
<<<<<<< HEAD

    // Define o relacionamento: uma mesa pode ter muitas reservas
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
=======
}
>>>>>>> 71a0c8232b9d8407a9e1c0f3ae088b5661282041
