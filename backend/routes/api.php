<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProviderController;
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

    Route::get('/{provider}/redirect', [ProviderController::class, 'redirectToProvider'])
        ->where('provider', 'google|facebook|apple');
});

Route::prefix('categories')->group(function(){
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('suppliers')->group(function(){
    Route::post('/', [SupplierController::class, 'store']);
    Route::get('/', [SupplierController::class, 'index']);
    Route::get('/{id}', [SupplierController::class, 'show']);
    Route::put('/{id}', [SupplierController::class, 'update']);
    Route::delete('/{id}', [SupplierController::class, 'destroy']);
});

Route::prefix('unitMeasures')->group(function(){
    Route::post('/', [UnitMeasureController::class, 'store']);
    Route::get('/', [UnitMeasureController::class, 'index']);
    Route::get('/{id}', [UnitMeasureController::class, 'show']);
    Route::put('/{id}', [UnitMeasureController::class, 'update']);
    Route::delete('/{id}', [UnitMeasureController::class, 'destroy']);
});

Route::prefix('ingredients')->group(function(){
    Route::post('/', [IngredientController::class, 'store']);
    Route::get('/', [IngredientController::class, 'index']);
    Route::get('/{id}', [IngredientController::class, 'show']);
    Route::put('/{id}', [IngredientController::class, 'update']);
    Route::delete('/{id}', [IngredientController::class, 'destroy']);
});

Route::prefix('products')->group(function(){
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
});
