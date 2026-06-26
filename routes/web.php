<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::resource('beneficiaries', BeneficiaryController::class);
    Route::patch('/beneficiaries/{beneficiary}/status/{status}',
    [App\Http\Controllers\BeneficiaryController::class, 'changeStatus'])
    ->name('beneficiaries.status');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
