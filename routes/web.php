<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\InsightsController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\CancellationController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\DistributorProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Retailers\RetailerDashboardController;
use App\Http\Controllers\Distributors\DistributorDashboardController;
use App\Http\Controllers\DistributorPageController;
use App\Http\Controllers\ProductDescController;

Route::get('/', function () {
    if (Auth::check()) {
        $userType = Auth::user()->user_type;
        $user = Auth::user();

        return match ($userType) {
            'admin' => redirect()->route('admin.dashboard'),
            'retailer' => redirect()->route('retailers.dashboard'),
            'distributor' => $user->profile_completed ?
                redirect()->route('distributors.dashboard') :
                redirect()->route('distributors.setup'),
            default => redirect()->route('login')
        };
    }
    return view('index');
});

// Admin Routes 
Route::middleware(['auth', 'checkRole:admin'])->name('admin.')->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('dashboard');
    // Add other admin routes here
});

// Retailer Routes
Route::middleware(['auth', 'checkRole:retailer'])->name('retailers.')->prefix('retailers')->group(function () {
    Route::get('/dashboard', [RetailerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/distributor/{id}', [DistributorController::class, 'show'])->name('distributor.show');
    Route::get('/distributors/{id}/products', [DistributorController::class, 'showProducts'])->name('distributor-page');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cart Routes - Consolidated
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/{id}', [CartController::class, 'show'])->name('cart.show');
    Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
});

// Distributor Routes
Route::middleware(['auth', 'verified', 'approved', 'checkRole:distributor'])->group(function () {
    Route::get('/distributors/setup', [DistributorProfileController::class, 'setup'])
        ->name('distributors.setup');
    Route::post('/profile/setup', [DistributorProfileController::class, 'updateSetup'])
        ->name('profile.updateSetup');

    Route::middleware(['profile.completed'])->group(function () {
        Route::get('/distributors', [DistributorDashboardController::class, 'index'])->name('distributors.index');
        Route::get('/dashboard', [DistributorDashboardController::class, 'dashboard'])->name('distributors.dashboard');

        // Product Routes   
        Route::get('/products', [ProductController::class, 'index'])->name('distributors.products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('distributors.products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('distributors.products.store');
        Route::get('/products/{id}', [ProductController::class, 'show'])->name('distributors.products.show');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('distributors.products.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('distributors.products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('distributors.products.destroy');

        // Order Routes
        Route::get('/orders', [OrderController::class, 'index'])->name('distributors.orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('distributors.orders.show');

        // Return Routes
        Route::get('/returns', [ReturnController::class, 'index'])->name('distributors.returns.index');
        // Cancellation Routes
        Route::get('/cancellations', [CancellationController::class, 'index'])->name('distributors.cancellations.index');

        // Delivery Routes
        Route::get('/delivery', [DeliveryController::class, 'index'])->name('distributors.delivery.index');

        // Inventory Routes
        Route::get('/inventory', [InventoryController::class, 'index'])->name('distributors.inventory.index');

        // Message Routes
        Route::get('/messages', [MessageController::class, 'index'])->name('distributors.messages.index');

        // Insights Routes
        Route::get('/insights', [InsightsController::class, 'index'])->name('distributors.insights.index');

        Route::get('/distributors/create', [DistributorController::class, 'create'])->name('distributors.create');
        Route::post('/distributors', [DistributorController::class, 'store'])->name('distributors.store');

        Route::get('/approval-waiting', [RegisteredUserController::class, 'approvalWaiting'])->name('auth.approval-waiting');
    });
});

Route::get('/download-credential', [ProfileController::class, 'downloadCredential'])
    ->name('download.credential')
    ->middleware('auth');

// Social Authentication Routes
Route::get('auth/facebook', [SocialAuthController::class, 'facebookRedirect'])->name('auth.facebook');
Route::get('auth/facebook/callback', [SocialAuthController::class, 'facebookCallback']);

Route::get('auth/google', [SocialAuthController::class, 'googleRedirect'])->name('auth.google');
Route::get('auth/google/callback', [SocialAuthController::class, 'googleCallback']);
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

require __DIR__ . '/auth.php';
