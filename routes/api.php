<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegistrationController;

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