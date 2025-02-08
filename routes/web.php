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

require __DIR__ . '/auth.php';  

Route::get('/distributors/create', [DistributorController::class, 'create'])->name('distributors.create');
Route::post('/distributors', [DistributorController::class, 'store'])->name('distributors.store');
Route::get('/distributors/approve/{id}', [DistributorController::class, 'approve'])->name('distributors.approve');
Route::get('/distributors/setup', [DistributorProfileController::class, 'setup'])->name('distributors.setup');
Route::post('/profile/setup', [DistributorProfileController::class, 'updateSetup'])->name('profile.updateSetup');


Route::get('/', function () {
    return view('index');
});

//Login Routes
Route::get('/retailers', [RetailerDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('retailers.dashboard');

Route::get('/distributors', [DistributorDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'approved']);

Route::get('/approval-waiting', function () {
    return view('auth.approval-waiting');
})->name('auth.approval-waiting');

// Distributor Routes
Route::middleware(['auth', 'verified', 'approved', 'distributor'])->prefix('distributors')->name('distributors.')->group(function () {
    Route::get('/dashboard', [DistributorDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('/cancellations', [CancellationController::class, 'index'])->name('cancellations.index');
    Route::get('/delivery', [DeliveryController::class, 'index'])->name('delivery.index');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/insights', [InsightsController::class, 'index'])->name('insights.index');
});

Route::get('/dashboard', function () {
    if (Auth::check()) {
        return Auth::user()->user_type === 'distributor'
            ? redirect()->route('distributors.dashboard')
            : redirect()->route('retailers.dashboard');
    }
    return redirect('/login');
})->name('dashboard');


// Cart Routes
Route::get('retailers/carts', [CartController::class, 'index'])->name('retailers.carts.index');
Route::post('retailers/carts', [CartController::class, 'add'])->name('retailers.carts.add');
Route::put('retailers/carts/{id}', [CartController::class, 'update'])->name('retailers.carts.update');
Route::delete('retailers/carts/{id}', [CartController::class, 'remove'])->name('retailers.carts.remove');


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

Route::get('auth/google', [SocialAuthController::class, 'googleRedirect'])->name('auth.google');
Route::get('auth/google/callback', [SocialAuthController::class, 'googleCallback']);

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

Route::get('/admin', [AdminDashboardController::class, 'index']);
