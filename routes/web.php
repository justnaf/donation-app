<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\DonationProgramController as AdminDonationProgramController;
use App\Http\Controllers\Admin\DonationController as AdminDonationController;
use App\Http\Controllers\Admin\DonationCategoryController as AdminDonationCategoryController;
use App\Http\Controllers\Admin\ProgramUpdateController as AdminProgramUpdateController;
use App\Http\Controllers\DonationProgramController;
use App\Http\Controllers\DonationController;

Route::get('/', [DonationProgramController::class, 'index'])->name('home');
Route::get('/programs', [DonationProgramController::class, 'indexPrograms'])->name('programs.index');
Route::get('/programs/{program}/show', [DonationProgramController::class, 'showProgram'])->name('programs.show');
Route::post('/donations', [DonationController::class, 'store'])->name('donations.store');
Route::get('/donations/{donation:order_id}/status', [DonationController::class, 'show'])->name('donations.status');
Route::get('/donations/{donation:order_id}/check-status', [DonationController::class, 'checkStatus'])->name('donations.checkStatus');

Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::resource('programs', AdminDonationProgramController::class);
    Route::patch('programs/{program}/status', [AdminDonationProgramController::class, 'updateStatus'])->name('programs.update.status');
    Route::post('programs/{program}/donations', [AdminDonationController::class, 'store'])->name('programs.donations.store');
    Route::get('programs/{program}/donations', [AdminDonationController::class, 'index'])->name('programs.donations.index');
    Route::resource('donation-categories', AdminDonationCategoryController::class)->except(['show']);
    Route::get('/news', [AdminProgramUpdateController::class, 'index'])->name('news.index');
    Route::post('/news/{program}', [AdminProgramUpdateController::class, 'store'])->name('news.store');
    Route::get('/news/create/{program}', [AdminProgramUpdateController::class, 'create'])->name('news.create');
    Route::get('/news/{update}/edit', [AdminProgramUpdateController::class, 'edit'])->name('news.edit');
    Route::put('/news/{update}', [AdminProgramUpdateController::class, 'update'])->name('news.update');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
