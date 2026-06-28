<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/staff/login', [AuthController::class, 'login']);

// Public program routes
Route::get('/programs', [ProgramController::class, 'index']);
Route::get('/programs/{slug}', [ProgramController::class, 'show']);

// Public application routes
Route::post('/applications', [ApplicationController::class, 'store']);
Route::get('/applications/track', [ApplicationController::class, 'track']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/staff/logout', [AuthController::class, 'logout']);
    Route::get('/staff/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Program management (admin only)
    Route::post('/programs', [ProgramController::class, 'store']);
    Route::put('/programs/{program}', [ProgramController::class, 'update']);

    // Application management (admin only)
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::get('/applications/{id}', [ApplicationController::class, 'show']);
    Route::patch('/applications/{id}/status', [ApplicationController::class, 'updateStatus']);

});