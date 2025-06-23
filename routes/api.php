<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\Api\NotifController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication Routes (Public)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Protected Routes (Require JWT Authentication)
Route::middleware('auth:api')->group(function () {
    // Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::get('user/{id}', [AuthController::class, 'index']);
    });

    // Order Routes
    Route::get('orders/user/{user}', [OrderController::class, 'getByUser']);
    Route::post('orders', [OrderController::class, 'orderStore']);
    Route::put('orders/{id}', [OrderController::class, 'orderUpdate']);
    Route::delete('orders/{id}', [OrderController::class, 'orderDestroy']);

    // Cart Routes
    Route::get('cart', [OrderController::class, 'cartIndex']);
    Route::post('cart', [OrderController::class, 'cartStore']);
    Route::put('cart/{id}', [OrderController::class, 'cartUpdate']);
    Route::get('cart/{user_id}', [OrderController::class, 'cartDetail']);
    Route::delete('cart/{id}', [OrderController::class, 'cartDestroy']);

    // Payment Routes
    Route::get('payments', [OrderController::class, 'paymentIndex']);
    Route::post('payments', [OrderController::class, 'paymentStore']);

    // Review Routes (Authenticated users only)
    Route::apiResource('reviews', ReviewController::class)->only(['store', 'update', 'destroy']);

    // Notification Routes (Authenticated users only)
    Route::apiResource('notifications', NotifController::class)->only(['store', 'update', 'destroy']);

    // Payment Routes (Authenticated users only)
    Route::apiResource('payments', PaymentController::class)->only(['store', 'update', 'destroy']);
});

// Public Routes (No Authentication Required)
Route::get('order/{id}', [OrderController::class, 'show']); // Detail order (mungkin untuk admin atau publik)
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('sliders', SliderController::class)->only(['index', 'show']);
Route::apiResource('notifications', NotifController::class)->only(['index', 'show']);
Route::apiResource('reviews', ReviewController::class)->only(['index', 'show']);
Route::apiResource('orders', OrderController::class)->only(['index', 'show']);

// Midtrans Routes (Payment Gateway)
Route::post('create-transaction', [MidtransController::class, 'createTransaction']);
Route::post('midtrans-notification', [MidtransController::class, 'notificationHandler']);
Route::post('orders/update-status', [MidtransController::class, 'updateStatusFromClient']);
