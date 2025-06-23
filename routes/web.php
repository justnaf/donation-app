<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\DonationProgramController as AdminDonationProgramController;
use App\Http\Controllers\Admin\ProgramUpdateController;
use App\Http\Controllers\Admin\FundDisbursementController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('dashboard');
    Route::resource('programs', AdminDonationProgramController::class);
    Route::patch('programs/{program}/status', [AdminDonationProgramController::class, 'updateStatus'])->name('programs.update.status');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
