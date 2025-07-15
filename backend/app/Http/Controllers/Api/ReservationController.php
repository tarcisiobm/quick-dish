<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;

class ReservationController extends ApiController
{
    protected string $model = Reservation::class;
    protected string $name = 'Reservation';
    protected ?string $formRequest = ReservationRequest::class;
    protected array $with = ['user', 'table'];
}
