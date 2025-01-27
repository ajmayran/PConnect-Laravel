<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [UserDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/download-credential', [ProfileController::class, 'downloadCredential'])
    ->name('download.credential')
    ->middleware('auth');

    
require __DIR__.'/auth.php';
