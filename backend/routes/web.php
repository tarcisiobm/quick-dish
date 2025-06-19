<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmailVerificationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('api/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
