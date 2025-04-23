<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\CartDetail;
use App\Models\Distributors;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use App\Models\ReturnRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ReturnRequestItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class RetailerOrdersController extends Controller
{

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with(['orderDetails.product', 'distributor'])
            ->latest()
            ->paginate(3);

        return view('retailers.orders.index', compact('orders'));
    }

    public function toPay()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->where('status', 'processing')
            ->with(['distributor', 'orderDetails.product', 'payment', 'delivery']) // Added delivery relationship
            ->whereHas('payment', function ($query) {
                $query->where('payment_status', 'unpaid');
            })
            ->latest()
            ->paginate(3);

        return view('retailers.orders.to-pay', compact('orders'));
    }

    public function unpaid()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->where('status', 'delivered') // Orders that are delivered
            ->with(['distributor', 'orderDetails.product', 'payment'])
            ->whereHas('payment', function ($query) {
                $query->where('payment_status', 'unpaid'); // But payment is still unpaid
            })
            ->latest()
            ->paginate(3);

        return view('retailers.orders.unpaid', compact('orders'));
    }

    public function toReceive()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->where('status', 'processing')
            ->whereHas('delivery', function ($query) {
                $query->where('status', 'out_for_delivery');
            })
            ->with(['distributor', 'orderDetails.product', 'delivery'])
            ->latest()
            ->paginate(3);

        return view('retailers.orders.to-receive', compact('orders'));
    }

    public function completed()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->where('status', 'completed')

            ->with(['distributor', 'orderDetails.product'])
            ->latest()
            ->paginate(3);

        return view('retailers.orders.completed', compact('orders'));
    }

    public function cancelled()
    {
        $user = Auth::user();
        // Get both cancelled and rejected orders
        $orders = Order::where('user_id', $user->id)
            ->whereIn('status', ['cancelled', 'rejected'])
            ->with(['distributor', 'orderDetails.product'])
            ->latest()
            ->paginate(3);

        // Calculate total amount for each order
        foreach ($orders as $order) {
            $order->total_amount = $order->orderDetails->sum('subtotal');
        }

        return view('retailers.orders.cancelled', compact('orders'));
    }


    public function returned()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->whereIn('status', ['returned'])
            ->with(['distributor', 'orderDetails.product'])
            ->latest()
            ->paginate(3);

        return view('retailers.orders.returned', compact('orders'));
    }

    public function requestReturn(Request $request, Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::user()->id) {
            return redirect()->back()->with('error', 'You are not authorized to return this order.');
        }

        // Check if the order status allows return (only completed orders can be returned)
        if (!in_array($order->status, ['completed', 'delivered'])) {
            return redirect()->back()->with('error', 'Only completed and delivered orders can be returned.');
        }

        // Validate the request data
        $validated = $request->validate([
            'reason' => 'required|string',
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
            'proof_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // 5MB limit
            'products' => 'required|array',
            'products.*.selected' => 'sometimes',
            'products.*.quantity' => 'required_with:products.*.selected|integer|min:1',
        ]);

        // Check if at least one product is selected
        $hasSelectedProducts = false;
        foreach ($request->products as $detailId => $product) {
            if (isset($product['selected'])) {
                $hasSelectedProducts = true;
                break;
            }
        }

        if (!$hasSelectedProducts) {
            return redirect()->back()->with('error', 'Please select at least one product to return.');
        }

        // Start a transaction to ensure data consistency
        DB::beginTransaction();

        try {
            // Store the receipt file
            $receiptPath = $request->file('receipt')->store('receipts', 'public');

            // Store proof image if provided
            $proofImagePath = null;
            if ($request->hasFile('proof_image')) {
                $proofImagePath = $request->file('proof_image')->store('proofs', 'public');
            }

            // Create the return request
            $returnRequest = ReturnRequest::create([
                'order_id' => $order->id,
                'retailer_id' => Auth::id(),
                'reason' => $validated['reason'],
                'receipt_path' => $receiptPath,
                'proof_image' => $proofImagePath,
                'status' => 'pending'
            ]);

            // Process selected products
            foreach ($request->products as $detailId => $product) {
                if (isset($product['selected'])) {
                    // Get the order detail to validate the quantity
                    $orderDetail = OrderDetails::find($detailId);

                    if (!$orderDetail || $orderDetail->order_id !== $order->id) {
                        throw new \Exception('Invalid product selected for return.');
                    }

                    // Validate that the return quantity doesn't exceed the ordered quantity
                    $returnQuantity = (int)$product['quantity'];
                    if ($returnQuantity > $orderDetail->quantity) {
                        throw new \Exception('Return quantity cannot exceed the ordered quantity.');
                    }

                    // Create return request item
                    ReturnRequestItem::create([
                        'return_request_id' => $returnRequest->id,
                        'order_detail_id' => $detailId,
                        'quantity' => $returnQuantity
                    ]);
                }
            }

            // Send notification to distributor about the return request
            $this->notificationService->create(
                $order->distributor_id,
                'return_request',
                [
                    'title' => 'New Return Request',
                    'message' => "A return request has been submitted for order {$order->formatted_order_id}.",
                    'order_id' => $order->id,
                    'return_request_id' => $returnRequest->id
                ],
                $order->id
            );

            DB::commit();
            return redirect()->route('retailers.orders.completed')->with('success', 'Return request submitted successfully. We will review your request shortly.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Return request failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit return request: ' . $e->getMessage());
        }
    }

    public function cancelOrder(Request $request, Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::user()->id) {
            return redirect()->back()->with('error', 'You are not authorized to cancel this order.');
        }

        // Check if the order status allows cancellation (only pending and processing orders can be cancelled)
        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->back()->with('error', 'This order cannot be cancelled.');
        }

        // Start a transaction to ensure data consistency
        DB::beginTransaction();

        try {
            // Store the original status before updating
            $originalStatus = $order->status;

            $order->update([
                'status' => 'cancelled',
                'cancel_reason' => $request->input('cancel_reason') === 'other'
                    ? $request->input('custom_reason')
                    : $request->input('cancel_reason'),
                'status_updated_at' => now()
            ]);

            // Return stock to inventory if the order status was 'processing'
            if ($originalStatus === 'processing') {
                foreach ($order->orderDetails as $detail) {
                    $product = $detail->product;

                    // Handle batch-managed products differently
                    if ($product->isBatchManaged()) {
                        // Find the stock out records related to this order detail
                        $stockRecords = \App\Models\Stock::where('product_id', $detail->product_id)
                            ->where('type', 'out')
                            ->where('notes', 'like', '%Order ' . $order->formatted_order_id . '%')
                            ->get();

                        Log::info('Found stock records for batch product', [
                            'product_id' => $detail->product_id,
                            'order_id' => $order->id,
                            'formatted_order_id' => $order->formatted_order_id,
                            'records_count' => $stockRecords->count()
                        ]);

                        // If no specific stock records found, we need to handle this specially
                        if ($stockRecords->isEmpty()) {
                            Log::info('No stock records found, checking for soft-deleted batches first');

                            // Check for soft-deleted batches that were completely used by this order
                            $deletedBatches = \App\Models\ProductBatch::withTrashed()
                                ->where('product_id', $detail->product_id)
                                ->whereNotNull('deleted_at')
                                ->orderBy('expiry_date')
                                ->get();

                            Log::info('Found deleted batches count: ' . $deletedBatches->count());

                            $remainingQuantity = $detail->quantity;
                            $restoredDeletedBatches = false;

                            // First attempt: Try to distribute quantities across all deleted batches
                            // that might have been used for this order
                            foreach ($deletedBatches as $deletedBatch) {
                                if ($remainingQuantity <= 0) break;

                                Log::info('Examining deleted batch for restoration', [
                                    'batch_id' => $deletedBatch->id,
                                    'batch_number' => $deletedBatch->batch_number,
                                    'deleted_at' => $deletedBatch->deleted_at
                                ]);

                                // Get the original batch quantity from stock records if possible
                                // Attempt to find a matching stock out record from before the batch was deleted
                                $originalBatchQuantity = \App\Models\Stock::where('batch_id', $deletedBatch->id)
                                    ->where('type', 'out')
                                    ->where('notes', 'like', '%Order ' . $order->formatted_order_id . '%')
                                    ->sum('quantity');

                                // If we can't find the original quantity, make an educated guess
                                if ($originalBatchQuantity <= 0) {
                                    // This is a rough estimate - in a production app you might
                                    // want to store the original batch quantity in a log or additional field
                                    $originalBatchQuantity = min($remainingQuantity, 25); // Assume a reasonable default
                                    Log::info('Could not determine original batch quantity, using estimate', [
                                        'estimated_quantity' => $originalBatchQuantity
                                    ]);
                                } else {
                                    Log::info('Found original batch quantity from stock records', [
                                        'original_quantity' => $originalBatchQuantity
                                    ]);
                                }

                                // Restore the batch
                                $deletedBatch->restore();

                                // Use either the original quantity or remaining quantity, whichever is smaller
                                $quantityToRestore = min($originalBatchQuantity, $remainingQuantity);
                                $deletedBatch->quantity = $quantityToRestore;
                                $deletedBatch->save();

                                // Create stock record for the restoration
                                $stockInRecord = \App\Models\Stock::create([
                                    'product_id' => $product->id,
                                    'batch_id' => $deletedBatch->id,
                                    'type' => 'in',
                                    'quantity' => $quantityToRestore,
                                    'user_id' => Auth::id(),
                                    'notes' => 'Order ' . $order->formatted_order_id . ' cancelled - restored deleted batch #' . $deletedBatch->batch_number,
                                    'stock_updated_at' => now()
                                ]);

                                Log::info('Restored deleted batch', [
                                    'batch_id' => $deletedBatch->id,
                                    'batch_number' => $deletedBatch->batch_number,
                                    'stock_record_id' => $stockInRecord->id,
                                    'quantity' => $quantityToRestore
                                ]);

                                $remainingQuantity -= $quantityToRestore;
                                $restoredDeletedBatches = true;
                            }

                            // If there's still remaining quantity or no batches were restored
                            if ($remainingQuantity > 0) {
                                // Find the existing batch to add remaining quantities
                                $batch = \App\Models\ProductBatch::where('product_id', $detail->product_id)
                                    ->orderBy('expiry_date')
                                    ->first();

                                if ($batch) {
                                    $batch->quantity += $remainingQuantity;
                                    $batch->save();

                                    // Create a stock in record to track the return
                                    $stockInRecord = \App\Models\Stock::create([
                                        'product_id' => $product->id,
                                        'batch_id' => $batch->id,
                                        'type' => 'in',
                                        'quantity' => $remainingQuantity,
                                        'user_id' => Auth::id(),
                                        'notes' => 'Order ' . $order->formatted_order_id . ' cancelled - returned to batch #' . $batch->batch_number,
                                        'stock_updated_at' => now()
                                    ]);

                                    Log::info('Added remaining quantity to existing batch', [
                                        'stock_record_id' => $stockInRecord->id,
                                        'batch_id' => $batch->id,
                                        'batch_number' => $batch->batch_number,
                                        'quantity' => $remainingQuantity
                                    ]);
                                } else {
                                    // Create a new batch if none exists
                                    $batch = \App\Models\ProductBatch::create([
                                        'product_id' => $detail->product_id,
                                        'batch_number' => 'new-' . uniqid(),
                                        'quantity' => $remainingQuantity,
                                        'expiry_date' => now()->addMonths(6),
                                        'received_at' => now()
                                    ]);

                                    $stockInRecord = \App\Models\Stock::create([
                                        'product_id' => $product->id,
                                        'batch_id' => $batch->id,
                                        'type' => 'in',
                                        'quantity' => $remainingQuantity,
                                        'user_id' => Auth::id(),
                                        'notes' => 'Order ' . $order->formatted_order_id . ' cancelled - returned to new batch #' . $batch->batch_number,
                                        'stock_updated_at' => now()
                                    ]);

                                    Log::info('Created new batch and stock in record', [
                                        'batch_id' => $batch->id,
                                        'batch_number' => $batch->batch_number,
                                        'stock_record_id' => $stockInRecord->id,
                                        'quantity' => $remainingQuantity
                                    ]);
                                }
                            }
                        } else {
                            // Process each stock record to properly return quantities to appropriate batches
                            foreach ($stockRecords as $stockRecord) {
                                if ($stockRecord->batch_id) {
                                    // Check if the batch still exists or was deleted (empty)
                                    $batch = \App\Models\ProductBatch::withTrashed()->find($stockRecord->batch_id);

                                    if ($batch) {
                                        Log::info('Processing batch from stock record', [
                                            'batch_id' => $batch->id,
                                            'batch_number' => $batch->batch_number,
                                            'is_trashed' => $batch->trashed(),
                                            'current_quantity' => $batch->quantity,
                                            'adding_quantity' => $stockRecord->quantity
                                        ]);

                                        if ($batch->trashed()) {
                                            // If batch was deleted, restore it first
                                            $batch->restore();
                                            $batch->quantity = $stockRecord->quantity;
                                        } else {
                                            // If batch exists, just add the quantity back
                                            $batch->quantity += $stockRecord->quantity;
                                        }

                                        // Save the batch with updated quantity
                                        $batch->save();

                                        // Double check the quantity was actually updated
                                        $updatedBatch = \App\Models\ProductBatch::find($batch->id);
                                        Log::info('Batch after update', [
                                            'batch_id' => $updatedBatch->id,
                                            'new_quantity' => $updatedBatch->quantity
                                        ]);

                                        // Create a stock in record to track the return
                                        $stockInRecord = \App\Models\Stock::create([
                                            'product_id' => $product->id,
                                            'batch_id' => $batch->id,
                                            'type' => 'in',
                                            'quantity' => $stockRecord->quantity,
                                            'user_id' => Auth::id(),
                                            'notes' => 'Order ' . $order->formatted_order_id . ' cancelled - returned to batch #' . $batch->batch_number,
                                            'stock_updated_at' => now()
                                        ]);

                                        Log::info('Created stock in record', [
                                            'stock_record_id' => $stockInRecord->id,
                                            'product_id' => $product->id,
                                            'batch_id' => $batch->id,
                                            'quantity' => $stockRecord->quantity
                                        ]);
                                    }
                                }
                            }
                        }
                    } else {
                        // Handle non-batch products
                        $stockInRecord = \App\Models\Stock::create([
                            'product_id' => $detail->product_id,
                            'batch_id' => null,
                            'type' => 'in',
                            'quantity' => $detail->quantity,
                            'user_id' => Auth::id(),
                            'notes' => 'Order ' . $order->formatted_order_id . ' cancelled',
                            'stock_updated_at' => now()
                        ]);

                        Log::info('Created non-batch stock in record', [
                            'stock_record_id' => $stockInRecord->id,
                            'product_id' => $detail->product_id,
                            'quantity' => $detail->quantity
                        ]);
                    }

                    // Update product's stock_updated_at timestamp
                    $product->update(['stock_updated_at' => now()]);
                }
            }

            // Send notification to both retailer and distributor
            $this->notificationService->orderStatusChanged(
                $order->id,
                'cancelled',
                $order->user_id,
                $order->distributor_id
            );

            DB::commit();
            return redirect()->back()->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }

    public function placeOrder(Request $request, $distributorId)
    {
        try {
            $request->validate([
                'delivery_option' => 'required|in:default,other',
                'new_delivery_address' => 'nullable|required_if:delivery_option,other|string',
            ]);

            $user = Auth::user();

            if ($request->input('delivery_option') === 'default') {
                if (!$user->retailerProfile || !$user->retailerProfile->barangay_name) {
                    return redirect()->back()->with('error', 'No default delivery address found. Please provide a new address.');
                }
                $deliveryAddress = $user->retailerProfile->barangay_name .
                    ($user->retailerProfile->street ? ', ' . $user->retailerProfile->street : '');
            } else {
                $deliveryAddress = $request->input('new_delivery_address');
            }

            DB::beginTransaction();

            $cart = Cart::where('user_id', $user->id)
                ->where('distributor_id', $distributorId)
                ->first();

            if (!$cart) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Cart not found.');
            }

            $order = Order::create([
                'user_id' => $user->id,
                'distributor_id' => $distributorId,
                'status' => 'pending',
                'status_updated_at' => now(),
                'total_amount' => 0, // Will be updated after processing cart details
            ]);

            // Get cart details with product information
            $cartDetails = CartDetail::where('cart_id', $cart->id)->with('product')->get();
            $totalAmount = 0;

            // Log the cart details being processed
            Log::info('Processing cart details for order', [
                'order_id' => $order->id,
                'cart_id' => $cart->id,
                'items_count' => $cartDetails->count()
            ]);

            foreach ($cartDetails as $cartDetail) {
                $product = $cartDetail->product;

                // Calculate the correct final subtotal after discount
                $originalSubtotal = $cartDetail->price * $cartDetail->quantity;
                $discountAmount = $cartDetail->discount_amount ?? 0;
                $finalSubtotal = $originalSubtotal - $discountAmount;

                // Calculate free items for freebie discounts
                $freeItems = 0;
                if ($cartDetail->applied_discount && $cartDetail->free_items > 0) {
                    $freeItems = $cartDetail->free_items;
                }

                // Total quantity including free items
                $totalQuantity = $cartDetail->quantity + $freeItems;

                // Debug each cart detail with calculated values
                Log::info('Processing cart detail', [
                    'product_id' => $cartDetail->product_id,
                    'product_name' => $product->product_name,
                    'quantity' => $cartDetail->quantity,
                    'free_items' => $freeItems,
                    'total_quantity' => $totalQuantity,
                    'price' => $cartDetail->price,
                    'original_subtotal' => $originalSubtotal,
                    'discount_amount' => $discountAmount,
                    'final_subtotal' => $finalSubtotal,
                    'applied_discount' => $cartDetail->applied_discount
                ]);

                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $cartDetail->product_id,
                    'quantity' => $totalQuantity, // Include free items here
                    'price' => $cartDetail->price,
                    'subtotal' => $finalSubtotal, // Use calculated final subtotal
                    'delivery_address' => $deliveryAddress,
                    'discount_amount' => $discountAmount,
                    'free_items' => $freeItems,
                    'applied_discount' => $cartDetail->applied_discount
                ]);

                // Add this item's final subtotal to the order total
                $totalAmount += $finalSubtotal;
            }

            // Log the calculated order total
            Log::info('Calculated order total', [
                'order_id' => $order->id,
                'total_amount' => $totalAmount,
                'request_total' => $request->input('total_amount', 'N/A')
            ]);

            // Update order with total amount
            $order->total_amount = $totalAmount;
            $order->save();

            // Clear the cart after successful order creation
            $cart->details()->delete();
            $cart->delete();

            // Get distributor information for better notification message
            $distributor = Distributors::find($distributorId);

            // Send notification to distributor about new order
            $this->notificationService->newOrderNotification(
                $order->id,
                $user->id,
                $distributorId
            );

            // Add notification for retailer
            $distributorName = $distributor ? $distributor->company_name : 'the distributor';
            $this->notificationService->create(
                $user->id,
                'order_placed',
                [
                    'title' => 'Order Placed Successfully',
                    'message' => "Your order has been placed successfully and is awaiting confirmation from {$distributorName}.",
                    'order_id' => $order->id,
                    'recipient_type' => 'retailer'
                ],
                $order->id
            );

            DB::commit();

            return redirect()->route('retailers.orders.index')
                ->with('success', 'Order placed successfully with discounts applied.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error placing order: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while placing the order: ' . $e->getMessage());
        }
    }


    public function placeOrderAll(Request $request)
    {
        try {
            $request->validate([
                'delivery_option' => 'required|in:default,other',
                'new_delivery_address' => 'nullable|required_if:delivery_option,other|string',
            ]);

            $user = Auth::user();

            if ($request->input('delivery_option') === 'default') {
                // Check if retailer profile and address details exist
                if (!$user->retailerProfile || !$user->retailerProfile->barangay_name) {
                    return redirect()->back()->with('error', 'No default delivery address found. Please provide a new address.');
                }
                // Combine barangay name and street
                $deliveryAddress = $user->retailerProfile->barangay_name .
                    ($user->retailerProfile->street ? ', ' . $user->retailerProfile->street : '');
            } else {
                $deliveryAddress = $request->input('new_delivery_address');
            }

            // Start a transaction
            DB::beginTransaction();

            // Get all carts for this user
            $carts = Cart::where('user_id', $user->id)->get();

            if ($carts->isEmpty()) {
                DB::rollBack();
                return redirect()->back()->with('error', 'No items found in cart.');
            }

            // Group cart items by distributor
            $cartsByDistributor = [];
            foreach ($carts as $cart) {
                $distributorId = $cart->distributor_id;
                if (!isset($cartsByDistributor[$distributorId])) {
                    $cartsByDistributor[$distributorId] = [];
                }
                $cartsByDistributor[$distributorId][] = $cart;
            }

            $createdOrders = [];

            // Create one order per distributor
            foreach ($cartsByDistributor as $distributorId => $distributorCarts) {
                // Create single order for this distributor
                $order = Order::create([
                    'user_id' => $user->id,
                    'distributor_id' => $distributorId,
                    'status' => 'pending',
                    'status_updated_at' => now(),
                    'total_amount' => 0,  // Initialize with zero, will update after calculating details
                ]);

                $createdOrders[] = $order;
                $totalAmount = 0;

                // Process all cart items for this distributor
                foreach ($distributorCarts as $cart) {
                    // Get all cart details for this cart
                    $allCartDetails = CartDetail::where('cart_id', $cart->id)->with('product')->get();

                    // Log the cart details being processed
                    Log::info('Processing cart details for order', [
                        'order_id' => $order->id,
                        'cart_id' => $cart->id,
                        'items_count' => $allCartDetails->count()
                    ]);

                    // Add all cart details to the order
                    foreach ($allCartDetails as $cartDetail) {
                        // Calculate the correct final subtotal after discount
                        $originalSubtotal = $cartDetail->price * $cartDetail->quantity;
                        $discountAmount = $cartDetail->discount_amount ?? 0;
                        $finalSubtotal = $originalSubtotal - $discountAmount;

                        // Calculate free items for freebie discounts
                        $freeItems = 0;
                        if ($cartDetail->applied_discount && $cartDetail->free_items > 0) {
                            $freeItems = $cartDetail->free_items;
                        }

                        // Total quantity including free items
                        $totalQuantity = $cartDetail->quantity + $freeItems;

                        // Debug each cart detail with calculated values
                        Log::info('Processing cart detail', [
                            'product_id' => $cartDetail->product_id,
                            'product_name' => $cartDetail->product->product_name,
                            'quantity' => $cartDetail->quantity,
                            'free_items' => $freeItems,
                            'total_quantity' => $totalQuantity,
                            'price' => $cartDetail->price,
                            'original_subtotal' => $originalSubtotal,
                            'discount_amount' => $discountAmount,
                            'final_subtotal' => $finalSubtotal,
                            'applied_discount' => $cartDetail->applied_discount
                        ]);

                        OrderDetails::create([
                            'order_id' => $order->id,
                            'product_id' => $cartDetail->product_id,
                            'quantity' => $totalQuantity, // Include free items here
                            'price' => $cartDetail->price,
                            'subtotal' => $finalSubtotal, // Use calculated final subtotal
                            'delivery_address' => $deliveryAddress,
                            'discount_amount' => $discountAmount,
                            'free_items' => $freeItems,
                            'applied_discount' => $cartDetail->applied_discount
                        ]);

                        // Add this item's final subtotal to the order total
                        $totalAmount += $finalSubtotal;
                    }

                    // Clear the processed cart
                    $cart->details()->delete();
                    $cart->delete();
                }

                // Update order total
                $order->total_amount = $totalAmount;
                $order->save();

                // Get distributor information
                $distributor = Distributors::find($distributorId);

                // Send notification to distributor about new order
                $this->notificationService->newOrderNotification(
                    $order->id,
                    $user->id,
                    $distributorId
                );

                // Add notification for retailer
                $distributorName = $distributor ? $distributor->company_name : 'the distributor';
                $this->notificationService->create(
                    $user->id,
                    'order_placed',
                    [
                        'title' => 'Order Placed Successfully',
                        'message' => "Your order has been placed successfully and is awaiting confirmation from {$distributorName}.",
                        'order_id' => $order->id,
                        'recipient_type' => 'retailer'
                    ],
                    $order->id
                );
            }

            DB::commit();

            return redirect()->route('retailers.orders.index')
                ->with('success', 'Orders placed successfully with discounts applied.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error placing multiple orders: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while placing the order: ' . $e->getMessage());
        }
    }

    public function myPurchases()
    {
        $orders = Order::with(['orderDetails.product'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('retailers.profile.my-purchase', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('retailers.orders.show', compact('order'));
    }

    public function getOrderDetails(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['orderDetails.product']);
        return view('retailers.profile.order-details', compact('order'));
    }

    public function showOrderDetails($orderId)
    {
        try {
            // Load the order but make payment optional
            $query = Order::with(['orderDetails.product', 'distributor'])
                ->where('id', $orderId)
                ->where('user_id', Auth::id());

            // Only load payment if it exists (for non-pending orders)
            $order = $query->first();

            if (!$order) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
            }

            // Conditionally load the payment relationship if the order isn't pending
            if ($order->status !== 'pending') {
                $order->load('payment');
            }

            $html = view('retailers.profile.order-details', compact('order'))->render();

            return response()->json([
                'html' => $html,
                'order_id' => $order->formatted_order_id,
                'distributor_id' => $order->distributor_id
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading order details: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Error loading order details. Please try again.'
            ], 500);
        }
    }
    public function trackOrder(Request $request)
    {
        $trackingNumber = $request->tracking_number;

        // Find the delivery by tracking number
        $delivery = \App\Models\Delivery::where('tracking_number', $trackingNumber)
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id()); // Only allow the user to track their own orders
            })
            ->with(['order', 'order.distributor', 'order.orderDetails.product'])
            ->first();

        if (!$delivery) {
            return back()->with('error', 'No delivery found with this tracking number or it does not belong to your account.');
        }

        return view('retailers.orders.track', compact('delivery'));
    }

    public function viewReceipt(Order $order)
    {
        // Check if the order belongs to the current user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Ensure the order is complete
        if ($order->status !== 'completed') {
            return redirect()->back()->with('error', 'Receipt is only available for completed orders.');
        }

        // Load necessary relationships
        $order->load(['user', 'distributor', 'orderDetails.product', 'payment', 'delivery']);

        return view('retailers.orders.view-receipt', compact('order'));
    }

    public function downloadReceipt(Order $order)
    {
        // Check if the order belongs to the current user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Ensure the order is complete
        if ($order->status !== 'completed') {
            return redirect()->back()->with('error', 'Receipt is only available for completed orders.');
        }

        // Load necessary relationships
        $order->load(['user', 'distributor', 'orderDetails.product', 'payment', 'delivery']);

        // Generate PDF
        $pdf = PDF::loadView('retailers.orders.view-receipt', compact('order'));

        // Download the file
        return $pdf->download('receipt-' . $order->formatted_order_id . '.pdf');
    }
}
