<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Importing Auth facade
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\RegisteredUserController; // Importing RegisteredUserController
use App\Http\Controllers\Distributors\DistributorDashboardController;
use App\Http\Controllers\Retailers\RetailerDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController; // Importing AdminDashboardController
Route::get('/', function () {
    return view('index');
});

Route::get('/retailers', [RetailerDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('retailers.dashboard');

Route::get('/distributors', [DistributorDashboardController::class, 'index']) // Updated route
    ->middleware(['auth', 'verified'])
    ->name('distributors.dashboard'); // Updated name

Route::get('/dashboard', function () {
    if (Auth::check()) {
        return Auth::user()->user_type === 'distributor' 
            ? redirect()->route('distributors.dashboard') 
            : redirect()->route('retailers.dashboard');
    }
    if (Auth::check()) {
        return Auth::user()->user_type === 'distributor' 
            ? redirect()->route('distributors.dashboard') 
            : redirect()->route('retailers.dashboard');
    }
    return redirect('/login');
})->name('dashboard');

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
Route::get('/approval-waiting', [RegisteredUserController::class, 'approvalWaiting'])->name('auth.approval-waiting');


require __DIR__ . '/auth.php';

Route::get('auth/google', [SocialAuthController::class, 'googleRedirect'])->name('auth.google');
Route::get('auth/google/callback', [SocialAuthController::class, 'googleCallback']);


    // Add distributor route
Route::get('/distributor/{id}', [DistributorDashboardController::class, 'show'])
        ->name('distributor.show');

Route::get('/admin', [AdminDashboardController::class, 'show'])
        ->name('admin.dashboard');

require __DIR__.'/auth.php';

