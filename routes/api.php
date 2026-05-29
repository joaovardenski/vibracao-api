<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\MercadoPagoWebhookController;

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