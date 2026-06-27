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

Route::middleware(['auth'])->group(function () {

    // Beneficiary routes
    Route::get('/beneficiaries', [BeneficiaryController::class, 'index'])
        ->name('beneficiaries.index');

    Route::get('/beneficiaries/create', [BeneficiaryController::class, 'create'])
        ->name('beneficiaries.create')
        ->middleware('admin');

    Route::post('/beneficiaries', [BeneficiaryController::class, 'store'])
        ->name('beneficiaries.store')
        ->middleware('admin');

    Route::get('/beneficiaries/{id}', [BeneficiaryController::class, 'show'])
        ->name('beneficiaries.show');

    Route::get('/beneficiaries/{id}/edit', [BeneficiaryController::class, 'edit'])
        ->name('beneficiaries.edit')
        ->middleware('admin');

    Route::put('/beneficiaries/{id}', [BeneficiaryController::class, 'update'])
        ->name('beneficiaries.update')
        ->middleware('admin');

    Route::delete('/beneficiaries/{id}', [BeneficiaryController::class, 'destroy'])
        ->name('beneficiaries.destroy')
        ->middleware('admin');

    Route::patch('/beneficiaries/{beneficiary}/status/{status}',
        [BeneficiaryController::class, 'changeStatus'])
        ->name('beneficiaries.status')
        ->middleware('admin');

    Route::get('/beneficiaries-export-pdf',
        [BeneficiaryController::class, 'exportPdf'])
        ->name('beneficiaries.export.pdf')
        ->middleware('admin');

    Route::get('/beneficiaries-export-excel',
        [BeneficiaryController::class, 'exportExcel'])
        ->name('beneficiaries.export.excel')
        ->middleware('admin');

    // Super Admin only
    Route::get('/users', function () {
        return 'User management coming soon';
    })->name('users.index')->middleware('superadmin');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';