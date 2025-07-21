<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AvailableTablesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reservation_date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'guests_count' => 'required|integer|min:1',
        ]);

        $conflictingTableIds = Reservation::where('reservation_date', $validated['reservation_date'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) {
                    $q->where('start_time', '<', $validated['end_time'])
                        ->where('end_time', '>', $validated['start_time']);
                });
            })
            ->pluck('table_id');

        $availableTables = Table::where('status', true)
            ->where('capacity', '>=', $validated['guests_count'])
            ->whereNotIn('id', $conflictingTableIds)
            ->orderBy('number')
            ->get();

        return response()->json(['data' => $availableTables]);
    }
}
