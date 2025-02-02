<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Retailers\RetailerDashboardController;

Route::get('/', function () {
    return view('index');
});

Route::get('/retailers', [RetailerDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('retailers.dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/download-credential', [ProfileController::class, 'downloadCredential'])
    ->name('download.credential')
    ->middleware('auth');

    

// Social Authentication Routes
Route::get('auth/facebook', [SocialAuthController::class, 'facebookRedirect'])->name('auth.facebook');
Route::get('auth/facebook/callback', [SocialAuthController::class, 'facebookCallback']);

Route::get('auth/google', [SocialAuthController::class, 'googleRedirect'])->name('auth.google');
Route::get('auth/google/callback', [SocialAuthController::class, 'googleCallback']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Add distributor route
    Route::get('/distributor/{id}', [DistributorPageController::class, 'show'])
        ->name('distributor.show');
});

require __DIR__.'/auth.php';
