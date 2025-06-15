<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\WebViewController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rute untuk beranda
Route::get('/', [WebViewController::class, 'index'])->name('beranda');

// Rute untuk menu
Route::get('/menu', [WebViewController::class, 'menu'])->name('menu');

// Rute untuk promo
Route::get('/promo', [WebViewController::class, 'promo'])->name('promo');

// Rute untuk berita
Route::get('/berita', function () {
    return view('berita');
})->name('berita');

// Rute untuk kemitraan
Route::get('/kemitraan', function () {
    return view('kemitraan');
})->name('kemitraan');

// Rute untuk hubungi kami
Route::get('/hubungi-kami', function () {
    return view('hubungi-kami');
})->name('hubungi-kami');

// Rute untuk admin
Route::get('/admin/login', [AdminLoginController::class, 'index'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Rute untuk admin signup
Route::get('/admin/signup', [AdminLoginController::class, 'showSignupForm'])->name('admin.signup');
Route::post('/admin/signup', [AdminLoginController::class, 'signup'])->name('admin.signup.submit');

// Admin Routes dengan middleware auth dan admin
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/orders/report', [OrderController::class, 'report'])->name('admin.orders.report');
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::get('/products/report', [ProductController::class, 'report'])->name('admin.products.report');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('admin.products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders');
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::get('/admin/orders/pdf', [OrderController::class, 'generatePdf'])->name('admin.orders.pdf');
    Route::get('/categories', [DashboardController::class, 'categories'])->name('admin.categories');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('admin.reports');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('admin.settings');
    Route::get('/search', [DashboardController::class, 'search'])->name('admin.search');
});
