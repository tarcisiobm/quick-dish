<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'job_title',
        'salary',
        'hire_date',
        'termination_date',
        'work_schedule',
    ];

    protected $casts = [
        'hire_date' => 'date:Y-m-d',
        'termination_date' => 'date:Y-m-d',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
