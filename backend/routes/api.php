<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\UnitMeasureController;
use App\Http\Controllers\Api\PaymentTypeController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AvailableTablesController;
use App\Http\Controllers\Api\ReviewController;

Route::prefix('/auth')->group(function () {
    Route::post('/sign-up', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/recover-password', [AuthController::class, 'recoverPassword'])->middleware('throttle:6,1');
    Route::post('/validate-token', [AuthController::class, 'validateToken'])->middleware('throttle:6,1');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:6,1');
    Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/resend-verification-email', [EmailVerificationController::class, 'resend'])->middleware('throttle:6,1');
    Route::get('/{provider}/redirect', [SocialAuthController::class, 'getAuthUrl'])->where('provider', 'google|facebook|apple');
});

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/ingredients', [IngredientController::class, 'index']);
Route::get('/reviews', [ReviewController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::get('/user', fn(Request $request) => $request->user()->load('employee'));
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/change-email', [AuthController::class, 'changeEmail']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    Route::get('/available-tables', AvailableTablesController::class);
    Route::get('/user/reservations', [ReservationController::class, 'userReservations']);

    Route::get('/user/orders', [OrderController::class, 'userOrders']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    Route::apiResource('ingredients', IngredientController::class)->except(['index']);
    Route::post('/reviews', [ReviewController::class, 'store']);

    Route::apiResources([
        'suppliers' => SupplierController::class,
        'unitMeasures' => UnitMeasureController::class,
        'tables' => TableController::class,
        'paymentTypes' => PaymentTypeController::class,
        'addresses' => AddressController::class,
        'deliveries' => DeliveryController::class,
        'employees' => EmployeeController::class,
        'coupons' => CouponController::class,
        'reservations' => ReservationController::class,
        'orders' => OrderController::class,
        'users' => UserController::class,
    ]);
});
