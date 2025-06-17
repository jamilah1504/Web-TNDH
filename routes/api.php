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

// Authentication Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('/user/{user}', [AuthController::class, 'index']);

// Protected Routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    // Order Routes
    
});
// Order routes
Route::get('/order/{user}', [OrderController::class, 'index']);
Route::get('/order-detail/{order}', [OrderController::class, 'show']);
Route::get('/orders/user/{users}', [OrderController::class, 'getByUser']);
Route::post('/orders', [OrderController::class, 'orderStore']);
Route::put('/orders/{id}', [OrderController::class, 'orderUpdate']);
Route::delete('/orders/{id}', [OrderController::class, 'orderDestroy']);

// Payment routes
Route::get('/payments', [OrderController::class, 'paymentIndex']);
Route::post('/payments', [OrderController::class, 'paymentStore']);
// Cart routes
Route::get('/cart', [OrderController::class, 'cartIndex']);
Route::post('/cart', [OrderController::class, 'cartStore']);
Route::put('/cart/{id}', [OrderController::class, 'cartUpdate']);
Route::get('/cart/{user_id}', [OrderController::class, 'cartDetail']);
Route::delete('/cart/{id}', [OrderController::class, 'cartDestroy']);

// Review Routes
Route::apiResource('reviews', ReviewController::class);

// Payment Routes
Route::apiResource('payments', PaymentController::class);

// Notification Routes
Route::apiResource('notifications', NotifController::class);

// Public Routes
Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('sliders', SliderController::class);
Route::apiResource('notifications', NotifController::class);
Route::apiResource('reviews', ReviewController::class);
Route::apiResource('orders', OrderController::class);

Route::post('/create-transaction', [MidtransController::class, 'createTransaction']);
Route::post('/midtrans-notification', [MidtransController::class, 'notificationHandler']);
Route::post('/orders/update-status', [MidtransController::class, 'updateStatusFromClient']);