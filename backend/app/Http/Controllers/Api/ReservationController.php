<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
// use Illuminate\Support\Facades\Auth; // Removido para testes sem autenticação

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $user = Auth::user(); // Removido para testes sem autenticação
        
        $query = Reservation::query();
        
        if ($request->has("date") && $request->date) {
            $query->whereDate("reservation_date", $request->date);
        }
        
        if ($request->has("user_id") && $request->user_id) {
            $query->where("user_id", $request->user_id);
        }
        
        $reservations = $query->orderBy("reservation_date", "desc")
                             ->orderBy("start_time", "desc") // Usando start_time em vez de reservation_time
                             ->get();
        
        return response()->json(["data" => $reservations], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "reservation_date" => "required|date|after_or_equal:today",
            "reservation_time" => "required|date_format:H:i",
            "number_of_guests" => "required|integer|min:1|max:10",
            "special_requests" => "nullable|string|max:500",
        ]);

        // Para testes sem autenticação, vamos usar um user_id fixo.
        // Em produção, você DEVE associar a reserva a um usuário real autenticado.
        $userId = 1; // ID de um usuário existente no seu banco de dados para testes.

        $reservation = new Reservation([
            "user_id" => $userId,
            "reservation_date" => $request->reservation_date,
            "start_time" => $request->reservation_time, // Mapeando para start_time
            "end_time" => date('H:i', strtotime($request->reservation_time . ' +2 hours')), // Calculando end_time (2h depois)
            "guests_count" => $request->number_of_guests, // Mapeando para guests_count
            "notes" => $request->special_requests, // Mapeando para notes
            "table_id" => 1, // Usando table_id fixo para testes (você pode ajustar)
            "status" => 1, // Status ativo
        ]);

        $reservation->save();

        return response()->json([
            "message" => "Reserva criada com sucesso!", 
            "reservation" => $reservation
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        return response()->json(["data" => $reservation], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        $request->validate([
            "reservation_date" => "sometimes|required|date|after_or_equal:today",
            "reservation_time" => "sometimes|required|date_format:H:i",
            "number_of_guests" => "sometimes|required|integer|min:1|max:10",
            "special_requests" => "nullable|string|max:500",
        ]);
        
        $updateData = [];
        
        if ($request->has('reservation_date')) {
            $updateData['reservation_date'] = $request->reservation_date;
        }
        
        if ($request->has('reservation_time')) {
            $updateData['start_time'] = $request->reservation_time;
            $updateData['end_time'] = date('H:i', strtotime($request->reservation_time . ' +2 hours'));
        }
        
        if ($request->has('number_of_guests')) {
            $updateData['guests_count'] = $request->number_of_guests;
        }
        
        if ($request->has('special_requests')) {
            $updateData['notes'] = $request->special_requests;
        }
        
        $reservation->update($updateData);
        
        return response()->json([
            "message" => "Reserva atualizada com sucesso!",
            "reservation" => $reservation
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        $reservation->delete();
        
        return response()->json(["message" => "Reserva cancelada com sucesso!"], 200);
    }
    
    /**
     * Get user's own reservations
     */
    public function myReservations()
    {
        // $user = Auth::user(); // Removido para testes sem autenticação
        
        $reservations = Reservation::orderBy("reservation_date", "desc")
                                  ->orderBy("start_time", "desc") // Usando start_time
                                  ->get();
        
        return response()->json(["data" => $reservations], 200);
    }
}