<?php

use App\Http\Controllers\Api\Admin\AdminManagementController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\RegistrationManagementController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\MercadoPago\MercadoPagoWebhookController;
use App\Http\Controllers\Api\Registration\RegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public & Guest Routes
|--------------------------------------------------------------------------
*/

// Auth (Guest / Rate Limited)
Route::post('/login', [AuthController::class, 'login'])
    ->name('api.login')
    ->middleware('throttle:10,1');

// Webhooks
Route::post('/webhooks/mercado-pago', [MercadoPagoWebhookController::class, 'handle'])
    ->name('api.webhooks.mercado-pago')
    ->middleware(['throttle:100,1', 'mp.signature']);

// Public Registrations & Orders
Route::prefix('registrations')->name('api.registrations.')->group(function () {
    Route::post('/', [RegistrationController::class, 'store'])
        ->name('store')
        ->middleware('throttle:5,1');

    Route::get('/status/{cpf}', [RegistrationController::class, 'statusByCpf'])
        ->name('status-by-cpf')
        ->middleware('throttle:10,1');
});

Route::prefix('orders')->name('api.orders.')->group(function () {
    Route::get('/{order}/status', [RegistrationController::class, 'status'])
        ->name('status');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // User Profile & Session
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    | Prefixed by 'admin/' and sharing the same auth context.
    */
    Route::prefix('admin')->name('api.admin.')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Registration Management
        Route::prefix('registrations')->name('registrations.')->group(function () {
            Route::get('/', [RegistrationManagementController::class, 'index'])->name('index');
            Route::post('/', [RegistrationManagementController::class, 'store'])->name('store');
            Route::get('/export-pdf', [RegistrationManagementController::class, 'exportPdf'])->name('export-pdf');
            Route::get('/{order}', [RegistrationManagementController::class, 'show'])->name('show');
        });

        // Admin User CRUD (index, store, show, update, destroy)
        Route::apiResource('admins', AdminManagementController::class);
    });
});