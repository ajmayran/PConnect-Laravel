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
use App\Http\Controllers\CancellationController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\DistributorPageController;
use App\Http\Controllers\Retailers\CartController;
use App\Http\Controllers\Distributors\DistributorDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\RegisteredUserController; 
use App\Http\Controllers\Retailers\RetailerDashboardController;



require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('index');
});

//Login Routes
Route::get('/retailers', [RetailerDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('retailers.dashboard');

Route::get('/distributors', [DistributorDashboardController::class, 'index']) 
    ->middleware(['auth', 'verified'])
    ->name('distributors.dashboard'); 

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

Route::get('/distributors/orders', [OrderController::class, 'index'])->name('distributors.orders.index');
Route::get('/distributors/orders/{id}', [OrderController::class, 'show'])->name('distributors.orders.show');

Route::get('/distributors/products', [ProductController::class, 'index'])->name('distributors.products.index');
Route::get('/distributors/products/create', [ProductController::class, 'create'])->name('distributors.products.create');
Route::post('/distributors/products', [ProductController::class, 'store'])->name('distributors.products.store');


Route::get('/distributors/returns', [ReturnController::class, 'index'])->name('distributors.returns.index');
Route::get('/distributors/cancellations', [CancellationController::class, 'index'])->name('distributors.cancellations.index');
Route::get('/distributors/delivery', [DeliveryController::class, 'index'])->name('distributors.delivery.index');
Route::get('/distributors/inventory', [InventoryController::class, 'index'])->name('distributors.inventory.index');
Route::get('/distributors/messages', [MessageController::class, 'index'])->name('distributors.messages.index');
Route::get('/distributors/insights', [InsightsController::class, 'index'])->name('distributors.insights.index');

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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [RetailerDashboardController::class, 'index'])->name('dashboard');

    // Add distributor route
    Route::get('/distributors', [DistributorPageController::class, 'index'])->name('distributors');
    Route::get('/distributors', [DistributorPageController::class, 'show'])->name('distributor.show');
    // Add distributor route
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');

    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});
require __DIR__.'/auth.php';
