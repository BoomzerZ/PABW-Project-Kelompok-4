<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth routes - rate limited: max 5 attempts per 15 minutes
Route::middleware('throttle:5,15')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [ProfileController::class, 'show']);
    Route::put('/user', [ProfileController::class, 'update']);
});

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/context', [ProductController::class, 'aiContext']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::post('/chat', [ChatController::class, 'chat']);
Route::get('/chat/history', [ChatController::class, 'history'])->middleware('auth:sanctum');
Route::get('/health/ollama', [ChatController::class, 'healthCheck']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);

    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/dummy', [NotificationController::class, 'triggerDummy']);

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/pay', [PaymentController::class, 'pay']);

    Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);
});

// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/products', [AdminController::class, 'listProducts']);
    Route::post('/products', [AdminController::class, 'storeProduct']);
    Route::post('/products/upload-image', [AdminController::class, 'uploadImage']);
    Route::put('/products/{id}', [AdminController::class, 'updateProduct']);
    Route::delete('/products/{id}', [AdminController::class, 'destroyProduct']);

    Route::get('/orders', [AdminController::class, 'listOrders']);
    Route::put('/orders/{id}/status', [AdminController::class, 'updateOrderStatus']);

    Route::get('/coupons', [AdminController::class, 'listCoupons']);
    Route::post('/coupons', [AdminController::class, 'storeCoupon']);
    Route::put('/coupons/{id}', [AdminController::class, 'updateCoupon']);
    Route::delete('/coupons/{id}', [AdminController::class, 'destroyCoupon']);
});
