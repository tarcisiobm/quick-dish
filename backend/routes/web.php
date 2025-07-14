<?php

use App\Http\Controllers\Api\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('api/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleCallback'])
    ->where('provider', 'google|facebook|apple');
