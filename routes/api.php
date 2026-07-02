<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\ApplicantController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SurveyController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\DistributionController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\ExpenseController;

// ── Public routes ─────────────────────────────────────────────────────────────
Route::post('/staff/login', [AuthController::class, 'login']);
Route::get('/programs', [ProgramController::class, 'index']);
Route::get('/programs/{slug}', [ProgramController::class, 'show']);
Route::post('/applications', [ApplicationController::class, 'store']);
Route::get('/applications/track', [ApplicationController::class, 'track']);

// ── Protected routes ──────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/staff/logout', [AuthController::class, 'logout']);
    Route::get('/staff/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Applications
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::get('/applications/{id}', [ApplicationController::class, 'show']);
    Route::patch('/applications/{id}', [ApplicationController::class, 'update']);
    Route::patch('/applications/{id}/status', [ApplicationController::class, 'updateStatus']);
    Route::get('/applications/{id}/logs', [ApprovalController::class, 'logs']);
    Route::post('/applications/{id}/approve', [ApprovalController::class, 'approve']);
    Route::post('/applications/{id}/reject', [ApprovalController::class, 'reject']);
    Route::post('/applications/{id}/hold', [ApprovalController::class, 'hold']);
    Route::post('/applications/{id}/assign', [ApprovalController::class, 'assign']);

    // Survey
    Route::get('/my-assignments', [SurveyController::class, 'myAssignments']);
    Route::post('/applications/{id}/survey', [SurveyController::class, 'store']);
    Route::get('/applications/{id}/survey', [SurveyController::class, 'show']);
    Route::put('/applications/{id}/survey', [SurveyController::class, 'update']);

    // Distribution
    Route::get('/distributions', [DistributionController::class, 'index']);
    Route::post('/applications/{id}/distribution', [DistributionController::class, 'store']);
    Route::get('/applications/{id}/distribution', [DistributionController::class, 'show']);
    Route::patch('/distributions/{id}/complete', [DistributionController::class, 'complete']);

    // Loans
    Route::get('/loans', [LoanController::class, 'index']);
    Route::get('/loans/overdue', [LoanController::class, 'overdue']);
    Route::post('/applications/{id}/loan', [LoanController::class, 'store']);
    Route::get('/loans/{id}', [LoanController::class, 'show']);
    Route::post('/loans/{id}/payment', [LoanController::class, 'recordPayment']);

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::post('/inventory', [InventoryController::class, 'store']);
    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock']);
    Route::get('/inventory/{id}', [InventoryController::class, 'show']);
    Route::put('/inventory/{id}', [InventoryController::class, 'update']);
    Route::patch('/inventory/{id}/stock-in', [InventoryController::class, 'stockIn']);
    Route::patch('/inventory/{id}/stock-out', [InventoryController::class, 'stockOut']);

    // Applicants
    Route::get('/applicants/{id}', [ApplicantController::class, 'show']);
    Route::put('/applicants/{id}', [ApplicantController::class, 'update']);
    Route::post('/applicants/{id}/blacklist', [ApplicantController::class, 'blacklist']);
    Route::post('/applicants/{id}/unblacklist', [ApplicantController::class, 'unblacklist']);

    // Users
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Audit logs
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
    Route::get('/audit-logs/{id}', [AuditLogController::class, 'show']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}', [NotificationController::class, 'update']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

    // Reports
    Route::get('/reports/applications', [ReportController::class, 'applications']);
    Route::get('/reports/loan-recovery', [ReportController::class, 'loanRecovery']);
    Route::get('/reports/distributions', [ReportController::class, 'distributions']);
    Route::get('/reports/inventory', [ReportController::class, 'inventory']);

    // Donations
    Route::apiResource('donations', DonationController::class);

    // Expenses
    Route::apiResource('expenses', ExpenseController::class);
});