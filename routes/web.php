<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\DistributorPageController;
use App\Http\Controllers\Retailers\CartController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Distributors\DistributorDashboardController;
use App\Http\Controllers\Retailers\RetailerDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ProductDescController;
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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [RetailerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::get('/product', [ProductDescController::class, 'show'])->name('retailer.product-description');

    // Add distributor route
    Route::get('/distributors', [DistributorPageController::class, 'index'])->name('distributors');
    Route::get('/distributors', [DistributorPageController::class, 'show'])->name('distributor.show');
    // Add distributor route

    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

});
require __DIR__.'/auth.php';

