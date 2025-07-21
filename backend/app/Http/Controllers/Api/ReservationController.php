<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ReservationController extends ApiController
{
    protected string $model = Reservation::class;
    protected string $name = 'Reservation';
    protected ?string $formRequest = ReservationRequest::class;
    protected array $with = ['user', 'table'];

    protected function beforeStore(array $validatedData, Request $request): void
    {
        $this->validateReservation($validatedData);
    }

    protected function beforeUpdate(array $validatedData, Model $resource, Request $request): void
    {
        $this->validateReservation($validatedData, $resource->id);
    }

    private function validateReservation(array $data, ?int $id = null): void
    {
        $table = Table::find($data['table_id']);

        if (!$table || !$table->status) {
            throw new ApiException(__('api.table_unavailable'));
        }

        if ($table->capacity < $data['guests_count']) {
            throw new ApiException(__('api.insufficient_table_capacity'));
        }

        $reservation = new Reservation($data);
        if ($reservation->hasConflict($id)) {
            throw new ApiException(__('api.reservation_conflict'));
        }
    }

    public function userReservations(Request $request)
    {
        $reservations = $this->model::where('user_id', $request->user()->id)
            ->with($this->with)
            ->orderBy('reservation_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return $this->successResponse(null, $reservations);
    }
}
