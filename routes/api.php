<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ApplicantController;
use App\Http\Controllers\Api\SurveyController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\DistributionController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\AuditLogController;
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

    // Program management
    Route::post('/programs', [ProgramController::class, 'store']);
    Route::put('/programs/{program}', [ProgramController::class, 'update']);

    // Application management
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::get('/applications/{id}', [ApplicationController::class, 'show']);
    Route::patch('/applications/{id}/status', [ApplicationController::class, 'updateStatus']);

    // Applicant management
    Route::get('/applicants', [ApplicantController::class, 'index']);
    Route::get('/applicants/{id}', [ApplicantController::class, 'show']);
    Route::put('/applicants/{id}', [ApplicantController::class, 'update']);
    Route::patch('/applicants/{id}/blacklist', [ApplicantController::class, 'blacklist']);
    Route::patch('/applicants/{id}/unblacklist', [ApplicantController::class, 'unblacklist']);

    // Survey routes
    Route::get('/my-assignments', [SurveyController::class, 'myAssignments']);
    Route::post('/applications/{applicationId}/survey', [SurveyController::class, 'store']);
    Route::get('/applications/{applicationId}/survey', [SurveyController::class, 'show']);
    Route::put('/applications/{applicationId}/survey', [SurveyController::class, 'update']);

    // Approval workflow
    Route::get('/applications/{applicationId}/logs', [ApprovalController::class, 'logs']);
    Route::post('/applications/{applicationId}/approve', [ApprovalController::class, 'approve']);
    Route::post('/applications/{applicationId}/reject', [ApprovalController::class, 'reject']);
    Route::post('/applications/{applicationId}/hold', [ApprovalController::class, 'hold']);
    Route::post('/applications/{applicationId}/assign', [ApprovalController::class, 'assign']);

    // Distribution
    Route::get('/distributions', [DistributionController::class, 'index']);
    Route::post('/applications/{applicationId}/distribution', [DistributionController::class, 'store']);
    Route::get('/applications/{applicationId}/distribution', [DistributionController::class, 'show']);
    Route::patch('/applications/{applicationId}/distribution/complete', [DistributionController::class, 'complete']);

    // Loan management
    Route::get('/loans', [LoanController::class, 'index']);
    Route::get('/loans/overdue', [LoanController::class, 'overdue']);
    Route::post('/applications/{applicationId}/loan', [LoanController::class, 'store']);
    Route::get('/loans/{id}', [LoanController::class, 'show']);
    Route::post('/loans/{loanId}/installments/{installmentId}/pay', [LoanController::class, 'recordPayment']);

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::post('/inventory', [InventoryController::class, 'store']);
    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock']);
    Route::get('/inventory/expiring-soon', [InventoryController::class, 'expiringSoon']);
    Route::get('/inventory/{id}', [InventoryController::class, 'show']);
    Route::put('/inventory/{id}', [InventoryController::class, 'update']);
    Route::patch('/inventory/{id}/stock-in', [InventoryController::class, 'stockIn']);
    Route::patch('/inventory/{id}/stock-out', [InventoryController::class, 'stockOut']);

    // Audit logs (superadmin only)
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
    Route::get('/audit-logs/{id}', [AuditLogController::class, 'show']);

    // User management
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

});