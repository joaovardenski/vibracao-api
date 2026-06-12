<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\MercadoPagoWebhookController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\RegistrationManagementController;
use App\Http\Controllers\Api\Admin\AdminManagementController;

// Authentication routes
Route::post('/login', [AuthController::class, 'login'])->name('api.login')
    ->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',      [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
});

// Registration routes
Route::post('/registrations', [RegistrationController::class, 'store'])->name('api.registrations.store')
    ->middleware('throttle:5,1');

// Webhook routes
Route::post('/webhooks/mercado-pago', [MercadoPagoWebhookController::class, 'handle'])->name('api.webhooks.mercado-pago')
    ->middleware('throttle:100,1')
    ->middleware('mp.signature');

// Order Status Update route
Route::get('/orders/{order}/status', [RegistrationController::class, 'status'])->name('api.orders.status');

// Admin routes
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/registrations', [RegistrationManagementController::class, 'index']);
    Route::post('/registrations', [RegistrationManagementController::class, 'store']);
    Route::get('/registrations/exportPdf', [RegistrationManagementController::class, 'exportPdf']);
    Route::get('/registrations/{order}', [RegistrationManagementController::class, 'show']);

    Route::get('/admins', [AdminManagementController::class, 'index']);
    Route::post('/admins', [AdminManagementController::class, 'store']);
    Route::get('/admins/{admin}', [AdminManagementController::class, 'show']);
    Route::put('/admins/{admin}', [AdminManagementController::class, 'update']);
    Route::delete('/admins/{admin}', [AdminManagementController::class, 'destroy']);
});