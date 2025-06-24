<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\DonationProgramController as AdminDonationProgramController;
use App\Http\Controllers\Admin\DonationController as AdminDonationController;
use App\Http\Controllers\DonationProgramController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\Admin\DonationCategoryController as AdminDonationCategoryController;

Route::get('/', [DonationProgramController::class, 'index'])->name('home');
Route::get('/programs', [DonationProgramController::class, 'indexPrograms'])->name('programs.index');
Route::get('/programs/{program}/show', [DonationProgramController::class, 'showProgram'])->name('programs.show');
Route::post('/donations', [DonationController::class, 'store'])->name('donations.store');


Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('dashboard');
    Route::resource('programs', AdminDonationProgramController::class);
    Route::patch('programs/{program}/status', [AdminDonationProgramController::class, 'updateStatus'])->name('programs.update.status');
    Route::post('programs/{program}/donations', [AdminDonationController::class, 'store'])->name('programs.donations.store');
    Route::get('programs/{program}/donations', [AdminDonationController::class, 'index'])->name('programs.donations.index');

    Route::resource('donation-categories', AdminDonationCategoryController::class)->except(['show']);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
