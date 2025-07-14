<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\UnitMeasureController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/auth')->group(function () {
    Route::post('/sign-up', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/resend-verification-email', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1');

    Route::post('/recover-password', [AuthController::class, 'recoverPassword'])
        ->middleware('throttle:6,1');
    Route::post('/validate-token', [AuthController::class, 'validateToken'])
        ->middleware('throttle:6,1');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:6,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', fn(Request $request) => $request->user());
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/change-email', [AuthController::class, 'changeEmail']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    Route::get('/{provider}/redirect', [SocialAuthController::class, 'getAuthUrl'])
        ->where('provider', 'google|facebook|apple');
});

Route::apiResources([
    'categories' => CategoryController::class,
    'suppliers' => SupplierController::class,
    'unitMeasures' => UnitMeasureController::class,
    'ingredients' => IngredientController::class,
    'products' => ProductController::class,
    'tables' => TableController::class
]);
