<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'table_id',
        'user_id',
        'reservation_date',
        'start_time',
        'end_time',
        'guests_count',
        'notes',
        'status',
    ];

    protected $casts = [
        'reservation_date' => 'date:Y-m-d',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'status' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function hasConflict(?int $excludeId = null): bool
    {
        return self::where('status', true)
            ->where('table_id', $this->table_id)
            ->where('reservation_date', $this->reservation_date)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) {
                $q->whereBetween('start_time', [$this->start_time, $this->end_time])
                    ->orWhereBetween('end_time', [$this->start_time, $this->end_time])
                    ->orWhere(fn($sub) => $sub->where('start_time', '<=', $this->start_time)
                        ->where('end_time', '>=', $this->end_time));
            })
            ->exists();
    }
}
