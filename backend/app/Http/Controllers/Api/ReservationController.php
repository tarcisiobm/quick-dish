<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::query();

        $query->with(['user', 'table']);

        if ($request->has("date") && $request->date) {
            $query->whereDate("reservation_date", $request->date);
        }

        if ($request->has("user_id") && $request->user_id) {
             $query->where("user_id", $request->user_id);
        }
        
        if ($request->has("status") && $request->status && $request->status !== 'all') {
            $query->where("status", $request->status);
        }

        $reservations = $query->orderBy("reservation_date", "desc")
                             ->orderBy("start_time", "desc")
                             ->get();

        return response()->json(["data" => $reservations], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            "reservation_date" => "required|date_format:Y-m-d|after_or_equal:today",
            "reservation_time" => "required|date_format:H:i",
            "number_of_guests" => "required|integer|min:1|max:10",
            "special_requests" => "nullable|string|max:500",
            "user_name" => "required|string|max:150",
            "user_email" => "required|email|max:150",
            "user_phone" => "nullable|string|max:20",
        ]);

        // $userId = Auth::check() ? Auth::id() : null;
        
        // if (!$userId) {
        //     return response()->json(['message' => 'Autenticação necessária para fazer uma reserva.'], 401);
        // }

        $reservationDate = Carbon::parse($request->reservation_date)->toDateString();
        $startTime = $request->reservation_time;
        $endTime = Carbon::parse($startTime)->addHours(2)->format('H:i');
        $guestsCount = $request->number_of_guests;

        $occupiedTableIds = Reservation::where('reservation_date', $reservationDate)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            })
            ->whereIn('status', [1])
            ->pluck('table_id')
            ->toArray();

        $availableTable = Table::where('capacity', '>=', $guestsCount)
                                ->whereNotIn('id', $occupiedTableIds)
                                ->where('status', true)
                                ->orderBy('capacity', 'asc')
                                ->first();

        if (!$availableTable) {
            return response()->json(['message' => 'Desculpe, não há mesas disponíveis para este número de pessoas e horário.'], 400);
        }

        try {
            $reservation = Reservation::create([
                "user_id" => $request->user_id,
                "table_id" => $availableTable->id,
                "guest_name" => $request->user_name,
                "guest_email" => $request->user_email,
                "guest_phone" => $request->user_phone,
                "reservation_date" => $reservationDate,
                "start_time" => $startTime,
                "end_time" => $endTime,
                "guests_count" => $guestsCount,
                "notes" => $request->special_requests,
                "status" => 1,
            ]);

            $reservation->load('user', 'table');

            return response()->json([
                "message" => "Reserva criada com sucesso!",
                "reservation" => $reservation
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Erro ao criar reserva no DB: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Ocorreu um erro interno ao processar sua reserva. Por favor, tente novamente.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $reservation = Reservation::with(['user', 'table'])->findOrFail($id);

        return response()->json(["data" => $reservation], 200);
    }

    public function update(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);

        $request->validate([
            "reservation_date" => "sometimes|required|date_format:Y-m-d|after_or_equal:today",
            "reservation_time" => "sometimes|required|date_format:H:i",
            "number_of_guests" => "sometimes|required|integer|min:1|max:10",
            "special_requests" => "nullable|string|max:500",
            "user_name" => "sometimes|required|string|max:150",
            "user_email" => "sometimes|required|email|max:150",
            "user_phone" => "nullable|string|max:20",
        ]);

        $updateData = [];

        if ($request->has('reservation_date')) {
            $updateData['reservation_date'] = $request->reservation_date;
        }

        if ($request->has('reservation_time')) {
            $updateData['start_time'] = $request->reservation_time;
            $updateData['end_time'] = Carbon::parse($request->reservation_time)->addHours(2)->format('H:i');
        }

        if ($request->has('number_of_guests')) {
            $updateData['guests_count'] = $request->number_of_guests;
        }

        if ($request->has('special_requests')) {
            $updateData['notes'] = $request->special_requests;
        }
        
        if ($request->has('user_name')) {
            $updateData['guest_name'] = $request->user_name;
        }
        if ($request->has('user_email')) {
            $updateData['guest_email'] = $request->user_email;
        }
        if ($request->has('user_phone')) {
            $updateData['guest_phone'] = $request->user_phone;
        }

        $reservation->update($updateData);

        $reservation->load('user', 'table');

        return response()->json([
            "message" => "Reserva atualizada com sucesso!",
            "reservation" => $reservation
        ], 200);
    }

    public function destroy(string $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        $reservation->delete();
        
        return response()->json(["message" => "Reserva cancelada com sucesso!"], 200);
    }
    
    public function myReservations()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $reservations = Reservation::where('user_id', $user->id)
                                 ->with(['user', 'table'])
                                 ->orderBy("reservation_date", "desc")
                                 ->orderBy("start_time", "desc")
                                 ->get();

        return response()->json(["data" => $reservations], 200);
    }
}