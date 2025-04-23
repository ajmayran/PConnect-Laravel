<?php


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\Distributor;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\CheckProductStatus;
use App\Http\Controllers\BroadcastAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Retailers\CartController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Retailers\BuynowController;
use App\Http\Controllers\Retailers\ReviewController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\AllRetailerController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Distributors\OrderController;
use App\Http\Controllers\Distributors\TruckController;
use App\Http\Controllers\Retailers\CheckoutController;
use App\Http\Controllers\Retailers\RetailerController;
use App\Http\Controllers\Admin\AdminRetailerController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Distributors\ReturnController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Distributors\OrderQrController;
use App\Http\Controllers\Distributors\PaymentController;
use App\Http\Controllers\Retailers\AllProductController;
use App\Http\Controllers\Distributors\DeliveryController;
use App\Http\Controllers\Distributors\DiscountController;
use App\Http\Controllers\Distributors\InsightsController;
use App\Http\Controllers\Retailers\ProductDescController;
use App\Http\Controllers\Distributors\DashboardController;
use App\Http\Controllers\Distributors\DiscountsController;
use App\Http\Controllers\Distributors\InventoryController;
use App\Http\Controllers\Retailers\RetailerNotifController;
use App\Http\Controllers\Distributors\DistributorController;
use App\Http\Controllers\Retailers\AllDistributorController;
use App\Http\Controllers\Retailers\RetailerOrdersController;
use App\Http\Controllers\Retailers\RetailerSearchController;
use App\Http\Controllers\Retailers\RetailerTicketController;
use App\Http\Controllers\Distributors\CancellationController;
use App\Http\Controllers\Distributors\ProductBatchController;
use App\Http\Controllers\Retailers\DistributorPageController;
use App\Http\Controllers\Retailers\RetailerMessageController;
use App\Http\Controllers\Retailers\RetailerProductController;
use App\Http\Controllers\Distributors\ReturnRequestController;
use App\Http\Controllers\Retailers\DistributorFollowController;
use App\Http\Controllers\Retailers\RetailerDashboardController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Distributors\RetailerActionsController;
use App\Http\Controllers\Distributors\RetailerProfileController;
use App\Http\Controllers\Distributors\DistributorNotifController;
use App\Http\Controllers\Retailers\RetailerCredentialsController;
use App\Http\Controllers\Distributors\DistributorTicketController;
use App\Http\Controllers\Distributors\DistributorMessageController;
use App\Http\Controllers\Distributors\DistributorProductController;
use App\Http\Controllers\Distributors\DistributorProfileController;
use App\Http\Controllers\Distributors\DistributorDashboardController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;



Route::middleware('auth')->group(function () {
    // Email verification routes - these are already in your file
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');

    // Also add the approval-waiting route for authenticated users
    Route::get('approval-waiting', [RegisteredUserController::class, 'approvalWaiting'])->name('auth.approval-waiting');

    Route::get('application-rejected', [RegisteredUserController::class, 'applicationRejected'])->name('auth.application-rejected');
});

Route::post('/broadcasting/auth', [BroadcastAuthController::class, 'authenticate'])
    ->middleware(['web', 'auth'])
    ->name('broadcasting.auth');

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

Route::middleware(['auth', 'verified', 'checkRole:retailer'])->name('retailers.')->prefix('retailers')->group(function () {
    Route::get('/credentials/reupload', [RetailerCredentialsController::class, 'showReuploadForm'])->name('credentials.reupload');
    Route::post('/credentials/reupload', [RetailerCredentialsController::class, 'processReupload'])->name('credentials.process-reupload');
});


// Admin Routes
Route::middleware(['auth', 'checkRole:admin'])->name('admin.')->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/distributors/pending', [Distributor::class, 'pendingDistributors'])->name('pendingDistributors');
    Route::post('/admin/accept-distributor/{id}', [Distributor::class, 'acceptDistributor'])->name('acceptDistributor');
    Route::post('/distributors/{id}/decline', [Distributor::class, 'declineDistributor'])->name('declineDistributor');
    Route::get('/admin/distributors/approved', [Distributor::class, 'approvedDistributors'])->name('approvedDistributors');
    Route::get('/distributors/rejected', [Distributor::class, 'rejectedDistributors'])->name('rejectedDistributors');
    Route::get('/admin/download-credential/{id}', [AdminDashboardController::class, 'downloadCredential'])->name('downloadCredential');
    Route::get('/admin/distributors/all', [Distributor::class, 'allDistributors'])->name('allDistributors');
    Route::get('/admin/retailers', [AllRetailerController::class, 'allRetailers'])->name('allRetailers');
    Route::get('/distributors/{id}/view-information', [Distributor::class, 'viewInformation'])->name('viewInformation');
    // Tickets Routes
    Route::get('/admin/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('/admin/tickets/resolved', [AdminTicketController::class, 'resolved'])->name('tickets.resolved');
    Route::get('/admin/tickets/rejected', [AdminTicketController::class, 'rejected'])->name('tickets.rejected');
    Route::get('/admin/tickets/{id}', [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::post('/admin/tickets/{id}/resolve', [AdminTicketController::class, 'resolve'])->name('tickets.resolve');
    Route::post('/admin/tickets/{id}/reject', [AdminTicketController::class, 'reject'])->name('tickets.reject');

    // Category 
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index'); // View all categories
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create'); // Create category form
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store'); // Store new category
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit'); // Edit category form
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update'); // Update category
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy'); // Delete category

    // All users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index'); // List all users

    // All Products Route
    Route::get('/admin/products/all', [AdminProductController::class, 'allProducts'])->name('allProducts');
    Route::get('/admin/distributor/{id}/products', [AdminProductController::class, 'distributorProducts'])->name('distributorProducts');

    // Retailer Routes
    Route::delete('/admin/product/{id}/remove', [AdminProductController::class, 'removeProduct'])->name('removeProduct');
    Route::get('/admin/dashboard/chart-data', [AdminDashboardController::class, 'getChartData'])->name('dashboard.chart-data');

    Route::get('admin/retailers/credentials', [AdminRetailerController::class, 'retailerCredentials'])->name('retailers.credentials');
    Route::post('admin/retailers/{id}/approve-credentials', [AdminRetailerController::class, 'approveCredentials'])->name('retailers.approve-credentials');
    Route::post('admin/retailers/{id}/reject-credentials', [AdminRetailerController::class, 'rejectCredentials'])->name('retailers.reject-credentials');
    Route::post('admin/retailers/{id}/reject-credentials', [AdminRetailerController::class, 'rejectCredential'])->name('retailers.reject-credentials');
});



// Retailer Routes
Route::middleware(['auth', 'verified', 'checkRole:retailer', 'check.distributor.block', 'check.retailer.credentials'])->name('retailers.')->prefix('retailers')->group(function () {
    Route::get('/dashboard', [RetailerDashboardController::class, 'index'])->name('dashboard');

    // Ticket Routes
    Route::get('/tickets/create', [RetailerTicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [RetailerTicketController::class, 'store'])->name('tickets.store');

    // Profile Routes
    Route::put('retailers/profile/update-retailer', [ProfileController::class, 'updateRetailerProfile'])->name('profile.update.retailer');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('retailers/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('retailers/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::get('profile/my-purchase', [RetailerOrdersController::class, 'myPurchases'])->name('profile.my-purchase');
    Route::get('/profile/{order}/order-details', [RetailerORdersController::class, 'getOrderDetails'])->name('profile.order-details');


    // Message Routes
    Route::get('/messages', [App\Http\Controllers\Retailers\RetailerMessageController::class, 'index'])->name('messages.index');
    Route::post('/messages/send', [App\Http\Controllers\Retailers\RetailerMessageController::class, 'sendMessage'])->name('messages.send');
    Route::get('/messages/unread-count', [App\Http\Controllers\Retailers\RetailerMessageController::class, 'getUnreadCount'])->name('messages.unread-count');
    Route::post('/messages/mark-read', [RetailerMessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::get('/messages/preview', [RetailerMessageController::class, 'getMessagePreviews'])->name('retailers.messages.preview');
    Route::get('/messages/show/{user}', [RetailerMessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/delete-conversation', [RetailerMessageController::class, 'deleteConversation'])->name('messages.delete-conversation');

    // Product Routes
    Route::get('/distributors/{id}', [DistributorPageController::class, 'show'])->name('distributor-page');
    Route::get('/products/{product}', [ProductDescController::class, 'show'])->name('products.show');
    Route::get('/all-products', [AllProductController::class, 'index'])->name('all-product');
    Route::get('/products/{product}', [ProductDescController::class, 'show'])->name('products.show');

    // Cart Routes 
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{cartDetail}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/update-quantities', [CartController::class, 'updateQuantities'])->name('cart.update-quantities');
    Route::delete('/cart/remove/{itemId}', [CartController::class, 'removeProduct'])->name('cart.remove-product');
    Route::delete('/cart/delete/{cartId}', [CartController::class, 'deleteCart'])->name('cart.delete');
    Route::post('/cart/update-quantities', [CartController::class, 'updateQuantities'])->name('cart.update-quantities');

    // Checkout Routes
    Route::get('/checkout-all', [CheckoutController::class, 'checkoutAll'])->name('checkout.all');
    Route::get('/checkout/{distributorId}', [CheckoutController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout/placeOrderAll', [RetailerOrdersController::class, 'placeOrderAll'])->name('checkout.placeOrderAll');
    Route::post('/checkout/placeOrder/{distributorId}', [RetailerOrdersController::class, 'placeOrder'])->name('checkout.placeOrder');

    // Direct purchase routes
    Route::post('/direct-purchase/buy-now', [BuynowController::class, 'buyNow'])->name('direct-purchase.buy-now');
    Route::get('/direct-purchase/checkout', [BuynowController::class, 'checkout'])->name('direct-purchase.checkout');
    Route::post('/direct-purchase/place-order', [BuynowController::class, 'placeOrder'])->name('direct-purchase.place-order');

    // Order Routes
    Route::post('/orders', [RetailerOrdersController::class, 'store'])->name('orders.store');
    Route::get('/orders', [RetailerOrdersController::class, 'index'])->name('orders.index');
    Route::get('/orders/to-pay', [RetailerOrdersController::class, 'toPay'])->name('orders.to-pay');
    Route::get('/orders/to-receive', [RetailerOrdersController::class, 'toReceive'])->name('orders.to-receive');
    Route::get('/orders/completed', [RetailerOrdersController::class, 'completed'])->name('orders.completed');
    Route::get('/orders/cancelled', [RetailerOrdersController::class, 'cancelled'])->name('orders.cancelled');
    Route::get('/orders/returned', [RetailerOrdersController::class, 'returned'])->name('orders.returned');
    Route::get('/orders/track', [RetailerOrdersController::class, 'trackOrder'])->name('orders.track');
    Route::get('/orders/unpaid', [RetailerOrdersController::class, 'unpaid'])->name('orders.unpaid');
    Route::get('/profile/{order}/order-details', [RetailerOrdersController::class, 'showOrderDetails']);

    Route::get('/orders/{order}', [RetailerOrdersController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [RetailerOrdersController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/orders/{order}/request-return', [RetailerOrdersController::class, 'requestReturn'])->name('orders.request-return');


    //Nav Routes
    Route::get('/all-distributors', [AllDistributorController::class, 'index'])->name('all-distributor');
    Route::get('/distributor/{id}', [DistributorController::class, 'show'])->name('distributor.show');

    //
    Route::get('/search', [RetailerSearchController::class, 'search'])->name('search');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('orders/{order}/view-receipt', [RetailerOrdersController::class, 'viewReceipt'])->name('orders.view-receipt');
    Route::get('orders/{order}/receipt/download', [RetailerOrdersController::class, 'downloadReceipt'])->name('orders.download-receipt');
    Route::post('/distributors/{id}/report', [DistributorPageController::class, 'reportDistributor'])->name('distributors.report');
    Route::post('/distributors/follow', [DistributorFollowController::class, 'toggleFollow'])->name('distributors.follow');
    Route::get('/distributors/{distributor}/follow-status', [DistributorFollowController::class, 'checkFollowStatus'])->name('distributors.follow-status');
    Route::post('/distributors/follow', [DistributorPageController::class, 'toggleFollow'])->name('distributors.follow');

    // Notification Routes
    Route::get('/notifications', [RetailerNotifController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [RetailerNotifController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/latest', [RetailerNotifController::class, 'getLatestNotifications'])->name('notifications.latest');
    Route::post('/notifications/mark-read', [RetailerNotifController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [RetailerNotifController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});


// Distributor Routes
Route::middleware(['auth', 'verified', 'approved', 'checkRole:distributor', 'profile.completed'])->group(function () {
    Route::get('/distributors/setup', [DistributorProfileController::class, 'setup'])->name('distributors.setup');
    Route::post('/profile/setup', [DistributorProfileController::class, 'updateSetup'])->name('profile.updateSetup');
    Route::get('/profile', [DistributorProfileController::class, 'edit'])->name('distributors.profile.edit');
    Route::patch('/profile/update', [DistributorProfileController::class, 'update'])->name('distributors.profile.update');
    Route::put('/profile/update-distributor', [DistributorProfileController::class, 'updateDistributorProfile'])->name('distributors.profile.update.distributor');
    Route::delete('/profile', [DistributorProfileController::class, 'destroy'])->name('distributors.profile.destroy');
    Route::post('/profile/update-password', [DistributorProfileController::class, 'updatePassword'])->name('distributors.profile.update-password');
    Route::get('/profile/orders-settings', [DistributorProfileController::class, 'ordersSettings'])->name('distributors.profile.orders-settings');
    Route::put('/profile/orders-settings', [DistributorProfileController::class, 'updateOrdersSettings'])->name('distributors.profile.update.orders');

    Route::get('/distributors', [DistributorDashboardController::class, 'index'])->name('distributors.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('distributors.dashboard');
    Route::get('/dashboard/sales-data', [DashboardController::class, 'getSalesData'])->name('distributors.dashboard.sales-data');

    // Product Routes   
    Route::get('/products', [DistributorProductController::class, 'index'])->name('distributors.products.index');
    Route::get('/products/create', [DistributorProductController::class, 'create'])->name('distributors.products.create');
    Route::post('/products', [DistributorProductController::class, 'store'])->name('distributors.products.store');
    Route::get('/products/{id}/edit', [DistributorProductController::class, 'edit'])->name('distributors.products.edit');
    Route::put('/products/{id}', [DistributorProductController::class, 'update'])->name('distributors.products.update');
    Route::delete('/products/{id}', [DistributorProductController::class, 'destroy'])->name('distributors.products.destroy');
    Route::put('/products/{id}/update-price', [DistributorProductController::class, 'updatePrice'])->name('distributors.products.updatePrice');
    Route::get('/products/list', [DistributorProductController::class, 'getProductsList'])->name('distributors.products.list');
    Route::get('/products/history', [DistributorProductController::class, 'history'])->name('distributors.products.history');
    Route::get('/distributors/products/check-sku', [DistributorProductController::class, 'checkSku'])->name('distributors.products.check-sku');

    // Order Routes
    Route::get('/orders', [OrderController::class, 'index'])->name('distributors.orders.index');
    Route::post('/orders/{order}/accept', [OrderController::class, 'acceptOrder'])->name('orders.accept');
    Route::post('/orders/{order}/reject', [OrderController::class, 'rejectOrder'])->name('orders.reject');
    Route::get('/orders/{id}/details', [OrderController::class, 'getOrderDetails'])->name('orders.details');
    Route::post('/toggle-order-acceptance', [OrderController::class, 'toggleOrderAcceptance'])->name('distributors.toggle-order-acceptance');
    Route::post('/orders/{order}/edit', [OrderController::class, 'editOrderQuantity'])->name('distributors.orders.edit');
    Route::get('/orders/{order}/detail', [OrderController::class, 'getOrderDetail'])->name('orders.detail');

    // Order QR Routes
    Route::get('/orders/{order}/qrcode', [OrderQrController::class, 'showQrCode'])->name('distributors.orders.qrcode');
    Route::get('/orders/verify/{token}', [OrderQrController::class, 'verifyOrder'])->name('distributors.orders.verify');
    Route::post('/orders/action/{token}', [OrderQrController::class, 'processAction'])->name('distributors.orders.action');
    Route::get('/orders/processing', [OrderQrController::class, 'getProcessingOrders'])->name('distributors.orders.processing');
    Route::post('/orders/batch-qrcode', [OrderQrController::class, 'generateBatchQrCodes'])->name('distributors.orders.batch-qrcode');

    // Return Routes
    Route::get('/returns', [ReturnRequestController::class, 'index'])->name('distributors.returns.index');
    Route::get('/returns/{id}', [ReturnRequestController::class, 'show'])->name('distributors.returns.show');
    Route::post('/returns/{returnRequest}/approve', [ReturnRequestController::class, 'approve'])->name('distributors.returns.approve');
    Route::post('/returns/{returnRequest}/reject', [ReturnRequestController::class, 'reject'])->name('distributors.returns.reject');
    Route::get('/returns/export', [ReturnRequestController::class, 'export'])->name('distributors.returns.export');
    Route::get('/returns/{id}', [ReturnRequestController::class, 'show'])->name('distributors.returns.show');

    Route::get('/retailers/{id}', [RetailerProfileController::class, 'show'])->name('distributors.retailers.show');
    Route::get('/retailers/{retailer}/orders', [RetailerProfileController::class, 'getRetailerOrders']);

    Route::post('/retailers/{retailer}/report', [RetailerActionsController::class, 'reportRetailer'])->name('distributors.retailers.report');
    Route::post('/retailers/{retailer}/block', [RetailerActionsController::class, 'toggleBlockRetailer'])->name('distributors.retailers.block');

    // Cancellation Routes
    Route::get('/cancellations', [CancellationController::class, 'index'])->name('distributors.cancellations.index');
    Route::get('/cancellations/{orderId}/details', [CancellationController::class, 'getOrderDetails'])->name('distributors.cancellations.details');
    Route::delete('/cancellations/batch-delete', [CancellationController::class, 'batchDelete'])->name('distributors.cancellations.batch-delete');
    Route::delete('/cancellations/{id}',  [CancellationController::class, 'delete'])->name('distributors.cancellations.delete');


    // Delivery Routes
    Route::get('/delivery', [DeliveryController::class, 'index'])->name('distributors.delivery.index');
    Route::post('/delivery/{id}/update-status', [DeliveryController::class, 'updateStatus'])->name('delivery.update-status');
    Route::post('/deliveries/{delivery}/mark-delivered', [DeliveryController::class, 'markDelivered'])->name('distributors.deliveries.mark-delivered');
    Route::post('/delivery/{delivery}/assign-truck', [TruckController::class, 'assignDelivery'])->name('distributors.delivery.assign-truck');
    Route::get('/delivery/{delivery}/scan-qr', [DeliveryController::class, 'scanQrCode'])->name('distributors.delivery.scan-qr-general');
    Route::post('/delivery/process-general-scan', [OrderQrController::class, 'processGeneralScan'])->name('distributors.delivery.process-general-scan');
    Route::post('/api/verify-qr-token', [OrderQrController::class, 'verifyQrToken'])->name('api.verify-qr-token');
    Route::get('/delivery/scan-qr', [OrderQrController::class, 'showGeneralQrScanner'])->name('distributors.delivery.scan-qr-general');

    // Inventory Routes
    Route::get('/inventory', [InventoryController::class, 'index'])->name('distributors.inventory.index');
    Route::put('/inventory/{id}/update-stock', [InventoryController::class, 'updateStock'])->name('distributors.inventory.updateStock');
    Route::get('/inventory/{productId}/batches', [InventoryController::class, 'getBatches'])->name('distributors.inventory.batches');
    Route::get('inventory/history', [InventoryController::class, 'history'])->name('distributors.inventory.history');
    Route::post('/inventory/toggle-restock-alert', [InventoryController::class, 'toggleRestockAlert'])->name('distributors.inventory.toggle-restock-alert');
    Route::post('/inventory/dismiss-restock-alert', [InventoryController::class, 'dismissRestockAlert'])->name('distributors.inventory.dismiss-restock-alert');

    // Message Routes
    Route::get('/messages', [DistributorMessageController::class, 'index'])->name('distributors.messages.index');
    Route::post('/messages/send', [DistributorMessageController::class, 'sendMessage'])->name('distributors.messages.send');
    Route::get('/messages/unread-count', [DistributorMessageController::class, 'getUnreadCount'])->name('distributors.messages.unread-count');
    Route::post('/messages/mark-read', [DistributorMessageController::class, 'markAsRead'])->name('distributors.messages.mark-read');
    Route::get('/messages/{retailerId}', [DistributorMessageController::class, 'show'])->name('distributors.messages.show');

    // Notification Routes
    Route::get('/notifications', [DistributorNotifController::class, 'index'])->name('distributors.notifications.index');
    Route::post('/notifications/mark-read', [DistributorNotifController::class, 'markAsRead'])->name('distributors.notifications.mark-read');
    Route::post('/notifications/mark-all-read', [DistributorNotifController::class, 'markAllAsRead'])->name('distributors.notifications.mark-all-read');
    Route::get('/notifications/unread-count', [DistributorNotifController::class, 'getUnreadCount'])->name('distributors.notifications.unread-count');
    Route::get('/notifications/latest', [DistributorNotifController::class, 'getLatestNotifications'])->name('distributors.notifications.latest');

    // Insights Routes
    Route::get('/insights', [InsightsController::class, 'index'])->name('distributors.insights.index');
    Route::get('/insights/data', [InsightsController::class, 'getInsightsData'])->name('distributors.insights.data');

    // Discount Routes
    Route::patch('discounts/{discount}/toggle', [DiscountsController::class, 'toggle'])->name('distributors.discounts.toggle');
    Route::get('/discounts', [DiscountsController::class, 'index'])->name('distributors.discounts.index');
    Route::get('/discounts/create', [DiscountsController::class, 'create'])->name('distributors.discounts.create');
    Route::post('/discounts', [DiscountsController::class, 'store'])->name('distributors.discounts.store');
    Route::get('discounts/{discount}/edit', [DiscountsController::class, 'edit'])->name('distributors.discounts.edit');
    Route::put('/discounts/{id}', [DiscountsController::class, 'update'])->name('distributors.discounts.update');
    Route::delete('/discounts/{id}', [DiscountsController::class, 'destroy'])->name('distributors.discounts.destroy');
    Route::get('discounts/expired', [DiscountsController::class, 'expired'])->name('distributors.discounts.expired');
    Route::get('/discounts/{id}', [DiscountsController::class, 'show'])->name('distributors.discounts.show');


    // Payment Routes
    Route::get('/payments', [PaymentController::class, 'index'])->name('distributors.payments.index');
    Route::put('/payments/{payment}/update-status', [PaymentController::class, 'updateStatus'])->name('distributors.payments.update-status');
    Route::delete('/payments/batch-delete', [PaymentController::class, 'batchDelete'])->name('distributors.payments.batch-delete');
    Route::get('/payments/history', [PaymentController::class, 'history'])->name('distributors.payments.history');

    // Truck Routes
    Route::get('/trucks', [TruckController::class, 'index'])->name('distributors.trucks.index');
    Route::post('/trucks', [TruckController::class, 'store'])->name('distributors.trucks.store');
    Route::get('/trucks/{truck}', [TruckController::class, 'show'])->name('distributors.trucks.show');
    Route::get('/trucks/{truck}/edit', [TruckController::class, 'edit'])->name('distributors.trucks.edit');
    Route::put('/trucks/{truck}', [TruckController::class, 'update'])->name('distributors.trucks.update');
    Route::delete('/trucks/{truck}', [TruckController::class, 'destroy'])->name('distributors.trucks.destroy');
    Route::get('/trucks/{truck}/locations', [TruckController::class, 'locations'])->name('distributors.trucks.locations');
    Route::post('/trucks/{truck}/out-for-delivery', [TruckController::class, 'outForDelivery'])->name('distributors.trucks.out-for-delivery');
    Route::get('/trucks/{truck}/delivery-history', [TruckController::class, 'deliveryHistory'])->name('distributors.trucks.delivery-history');
    Route::post('/move-to-truck', [TruckController::class, 'moveDeliveryToTruck'])->name('distributors.deliveries.move-to-truck');

    // Blocking routes
    Route::get('/blocking/blocked-retailers', [RetailerActionsController::class, 'blockedRetailers'])->name('distributors.blocking.blocked-retailers');
    Route::post('/retailers/{retailer}/block', [RetailerActionsController::class, 'toggleBlockRetailer'])->name('distributors.retailers.block');
    Route::get('/blocking/blocked-messages', [DistributorMessageController::class, 'blockedMessages'])->name('distributors.blocking.blocked-messages');
    Route::post('/messages/{retailerId}/block', [DistributorMessageController::class, 'toggleBlockMessages'])->name('distributors.messages.block');
    Route::delete('/messages/{blockId}/unblock', [DistributorMessageController::class, 'unblockMessages'])->name('distributors.messages.unblock');

    // Ticket Routes
    Route::get('/tickets/create', [DistributorTicketController::class, 'create'])->name('distributors.tickets.create');
    Route::post('/tickets', [DistributorTicketController::class, 'store'])->name('distributors.tickets.store');
});

// Social Authentication Routes

Route::get('auth/facebook', [SocialAuthController::class, 'facebookRedirect'])->name('auth.facebook');
Route::get('auth/facebook/callback', [SocialAuthController::class, 'facebookCallback']);

Route::get('auth/google', [SocialAuthController::class, 'googleRedirect'])->name('auth.google');
Route::get('auth/google/callback', [SocialAuthController::class, 'googleCallback']);
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

Route::get('regions', [AddressController::class, 'getRegions']);
Route::get('provinces/{regionCode}', [AddressController::class, 'getProvinces']);
Route::get('cities/{provinceCode}', [AddressController::class, 'getCities']);
Route::get('barangays/{cityCode}', [AddressController::class, 'getBarangays']);

Route::get('/api/debug/zamboanga', [AddressController::class, 'debugZamboangaData']);
require __DIR__ . '/auth.php';


//Distributor Subscription Route
Route::get('/subscription', function () {
    return view('distributors.subscription');
});
