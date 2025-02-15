<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Distributor;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Retailers\CartController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Retailers\RetailerProductController;
use App\Http\Controllers\Retailers\AllDistributorController;
use App\Http\Controllers\Retailers\AllProductController;
use App\Http\Controllers\Distributors\OrderController;
use App\Http\Controllers\Retailers\CheckoutController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Distributors\ReturnController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Distributors\MessageController;
use App\Http\Controllers\Distributors\DeliveryController;
use App\Http\Controllers\Distributors\InsightsController;
use App\Http\Controllers\Retailers\ProductDescController;
use App\Http\Controllers\Distributors\InventoryController;
use App\Http\Controllers\Retailers\RetailerOrderController;
use App\Http\Controllers\Distributors\DistributorController;
use App\Http\Controllers\Distributors\CancellationController;
use App\Http\Controllers\Retailers\RetailerDashboardController;
use App\Http\Controllers\Retailers\DistributorPageController;
use App\Http\Controllers\Distributors\DistributorProfileController;
use App\Http\Controllers\Distributors\DistributorDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Middleware\CheckProductStatus;

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

use App\Http\Controllers\Admin\AdminTicketController;

// Admin Routes
Route::middleware(['auth', 'checkRole:admin'])->name('admin.')->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/distributors/pending', [Distributor::class, 'pendingDistributors'])->name('pendingDistributors');
    Route::post('/admin/accept-distributor/{id}', [Distributor::class, 'acceptDistributor'])->name('acceptDistributor');
    Route::post('/admin/decline-distributor/{id}', [Distributor::class, 'declineDistributor'])->name('declineDistributor');
    Route::get('/admin/products/pending', [AdminProductController::class, 'pendingProducts'])->name('pendingProducts');
    Route::post('/admin/approve-product/{id}', [AdminProductController::class, 'approveProduct'])->name('approveProduct');
    Route::post('/admin/reject-product/{id}', [AdminProductController::class, 'rejectProduct'])->name('rejectProduct');
    Route::get('/admin/distributors/approved', [Distributor::class, 'approvedDistributors'])->name('approvedDistributors');
    Route::get('/admin/distributors/{id}/products', [Distributor::class, 'distributorProducts'])->name('distributorProducts');
    Route::delete('/admin/product/{id}/remove', [Distributor::class, 'removeProduct'])->name('removeProduct');

    Route::get('/admin/download-credential/{id}', [AdminDashboardController::class, 'downloadCredential'])->name('downloadCredential');

    // Tickets Routes
    Route::get('/admin/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('/admin/tickets/{id}', [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::get('/admin/tickets/resolved', [AdminTicketController::class, 'resolved'])->name('tickets.resolved');
    Route::get('/admin/tickets/rejected', [AdminTicketController::class, 'rejected'])->name('tickets.rejected');
    Route::post('/admin/tickets/{id}/resolve', [AdminTicketController::class, 'resolve'])->name('tickets.resolve');
    Route::post('/admin/tickets/{id}/reject', [AdminTicketController::class, 'reject'])->name('tickets.reject');
});

use App\Http\Controllers\Retailer\RetailerTicketController;

// Retailer Routes
Route::middleware(['auth', 'checkRole:retailer'])->name('retailers.')->prefix('retailers')->group(function () {
    Route::get('/dashboard', [RetailerDashboardController::class, 'index'])->name('dashboard');

    // Ticket Routes
    Route::get('/retailer/tickets/create', [RetailerTicketController::class, 'create'])->name('tickets.create');
    Route::post('/retailer/tickets', [RetailerTicketController::class, 'store'])->name('tickets.store');
    
    // Profile Routes
    Route::put('retailers/profile/update-retailer', [ProfileController::class, 'updateRetailerProfile'])->name('profile.update.retailer');
    Route::get('retailers/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('retailers/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('retailers/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Product Routes
    Route::get('/products', [RetailerProductController::class, 'index'])->name('products.index');
    Route::get('/distributors/{id}', [DistributorPageController::class, 'show'])->name('distributor-page');
    Route::post('/products', [RetailerProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [ProductDescController::class, 'show'])->name('products.show');

    // Cart Routes 
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{cartDetail}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/update-quantities', [CartController::class, 'updateQuantities'])->name('cart.update-quantities');
    Route::delete('/cart/remove/{itemId}', [CartController::class, 'removeProduct'])->name('cart.remove-product');
    Route::delete('/cart/delete/{cartId}', [CartController::class, 'deleteCart'])->name('cart.delete');

    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

    // Order Routes
    Route::post('/orders', [RetailerOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [RetailerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [RetailerOrderController::class, 'show'])->name('orders.show');

    //Nav Routes
    Route::get('/all-distributors', [AllDistributorController::class, 'index'])->name('all-distributor');
    Route::get('/distributor/{id}', [DistributorController::class, 'show'])->name('distributor.show');

    Route::get('/all-products', [AllProductController::class, 'index'])->name('all-product');
    Route::get('/products/{product}', [ProductDescController::class, 'show'])->name('products.show');
});

use App\Http\Controllers\Distributors\DistributorProductController;
// Distributor Routes
Route::middleware(['auth', 'verified', 'approved', 'checkRole:distributor', 'profile.completed'])->group(function () {
    Route::get('/distributors/setup', [DistributorProfileController::class, 'setup'])->name('distributors.setup');
    Route::post('/profile/setup', [DistributorProfileController::class, 'updateSetup'])->name('profile.updateSetup');

    Route::get('/distributors', [DistributorDashboardController::class, 'index'])->name('distributors.index');
    Route::get('/dashboard', [DistributorDashboardController::class, 'dashboard'])->name('distributors.dashboard');

    // Product Routes   
    Route::get('/products', [DistributorProductController::class, 'index'])->name('distributors.products.index');
    Route::get('/products/create', [DistributorProductController::class, 'create'])->name('distributors.products.create');
    Route::post('/products', [DistributorProductController::class, 'store'])->name('distributors.products.store');

    Route::get('/products/{id}/edit', [DistributorProductController::class, 'edit'])->name('distributors.products.edit');
    Route::put('/products/{id}', [DistributorProductController::class, 'update'])->name('distributors.products.update');
    Route::delete('/products/{id}', [DistributorProductController::class, 'destroy'])->name('distributors.products.destroy');

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

// Social Authentication Routes
Route::get('auth/facebook', [SocialAuthController::class, 'facebookRedirect'])->name('auth.facebook');
Route::get('auth/facebook/callback', [SocialAuthController::class, 'facebookCallback']);

Route::get('auth/google', [SocialAuthController::class, 'googleRedirect'])->name('auth.google');
Route::get('auth/google/callback', [SocialAuthController::class, 'googleCallback']);
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

require __DIR__ . '/auth.php';