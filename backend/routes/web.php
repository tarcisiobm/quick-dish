<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProviderController;

Route::get('api/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::get('auth/{provider}/callback', [ProviderController::class, 'handleProviderCallback'])
        ->where('provider', 'google|facebook|apple');
