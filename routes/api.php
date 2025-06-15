<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\RajaOngkirController;
use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\NotificationController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [UserController::class, 'logout']);

    // Product routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/categories', [ProductController::class, 'getCategories']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Promo routes
    Route::get('/promos', [PromoController::class, 'index']);
    Route::get('/promos/banners', [PromoController::class, 'getBanners']); 
    Route::get('/promos/{id}', [PromoController::class, 'show']);
    Route::get('/promos/by-category', [PromoController::class, 'getPromosByCategory']); // Rute baru untuk promo berdasarkan kategori
    Route::get('/promos/by-type', [PromoController::class, 'getPromosByType']); // Rute baru untuk promo berdasarkan tipe

    // Cart routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::post('/cart/clear', [CartController::class, 'clear']);
    Route::post('/cart/checkout', [CartController::class, 'checkout']);

    // Order routes
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/orders/{order}/check-payment', [OrderController::class, 'checkPaymentStatus']);
    Route::put('/orders/{order}/delivery-status', [OrderController::class, 'updateDeliveryStatus']);
    Route::post('/orders/{order}/pay', [OrderController::class, 'createPayment']);
    // Pindahkan route ini ke luar middleware auth:sanctum
    // Reservation routes
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::post('/reservations/{reservation}/pay', [ReservationController::class, 'createPayment']);
    Route::get('/reservations/time-slots', [ReservationController::class, 'getTimeSlots']);
    Route::post('/payments/reservations/midtrans-callback', [ReservationController::class, 'handleCallback']);
    
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/latest', [NotificationController::class, 'getLatestNotifications']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
        // RajaOngkir routes
        Route::prefix('rajaongkir')->group(function () {
            Route::get('/provinces', [RajaOngkirController::class, 'getProvinces']);
            Route::get('/cities', [RajaOngkirController::class, 'searchDestination']);
            Route::post('/costs', [RajaOngkirController::class, 'CheckOngkir']);
            Route::get('/track', [RajaOngkirController::class, 'trackDelivery']); // optional
        });
    });

// Remove the extra parenthesis and newline
// Payment callback routes (no auth required)
// Midtrans Callback URLs - harus di luar middleware auth:sanctum
Route::post('/midtrans/notif', [OrderController::class, 'handleCallback']);
// RajaOngkir Routes

// Notification routes
// Payment redirect endpoints (no auth required)
Route::get('/payment/finish', [OrderController::class, 'handlePaymentFinish']);
Route::get('/payment/unfinish', [OrderController::class, 'handlePaymentUnfinish']);
Route::get('/payment/error', [OrderController::class, 'handlePaymentError']);



