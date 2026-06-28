<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProgramController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/staff/login', [AuthController::class, 'login']);

// Public program routes
Route::get('/programs', [ProgramController::class, 'index']);
Route::get('/programs/{slug}', [ProgramController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/staff/logout', [AuthController::class, 'logout']);
    Route::get('/staff/me', [AuthController::class, 'me']);

    // Program management (admin only)
    Route::post('/programs', [ProgramController::class, 'store']);
    Route::put('/programs/{program}', [ProgramController::class, 'update']);

});