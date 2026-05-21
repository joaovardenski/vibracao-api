<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login'])->name('api.login')
    ->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',      [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
});