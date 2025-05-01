<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Order;
use App\Models\Stock;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OrderDetails;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;


class OrderController extends Controller
{

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_REJECTED = 'rejected';

    public function index()
    {
        $distributorId = Auth::user()->distributor->id;
        $status = request('status', self::STATUS_PENDING);
        $search = request('search');

        // Base query with relationships
        $query = Order::with(['orderDetails.product', 'user.retailerProfile'])
            ->where('distributor_id', $distributorId)
            ->where('status', $status);

        // Order by different columns based on status
        if ($status === self::STATUS_PENDING) {
            $query = $query->oldest();
        } else if ($status === self::STATUS_PROCESSING) {
            $query = $query->orderBy('status_updated_at', 'desc');
        } else {
            $query = $query->latest();
        }

        // Apply search
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('order_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                    });
            });
        }

        // Paginate the results
        $orders = $query->paginate(10);
        if (request()->has('search') || request()->has('status')) {
            $orders->appends(request()->only(['search', 'status']));
        }

        return view('distributors.orders.index', compact('orders'));
    }


    public function acceptOrder(Request $request, Order $order)
    {
        if ($order->status !== self::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be accepted.'
            ], 400);
        }

        try {
            DB::transaction(function () use ($order) {
                // Stock deduction logic - 
                foreach ($order->orderDetails as $detail) {
                    $product = $detail->product;
                    $quantityRemaining = $detail->quantity;

                    // Check stock availability
                    if ($product->isBatchManaged()) {
                        $totalBatchStock = $product->batches()->sum('quantity');
                        if ($totalBatchStock < $quantityRemaining) {
                            throw new \Exception("Insufficient stock for product {$product->product_name}. Available: {$totalBatchStock}, Required: {$quantityRemaining}");
                        }
                    } else {
                        $currentStock = Stock::where('product_id', $product->id)
                            ->where('type', 'in')
                            ->sum('quantity') - Stock::where('product_id', $product->id)
                            ->where('type', 'out')
                            ->sum('quantity');

                        if ($currentStock < $quantityRemaining) {
                            throw new \Exception("Insufficient stock for product {$product->product_name}. Available: {$currentStock}, Required: {$quantityRemaining}");
                        }
                    }

                    // Handle batch-managed products using FIFO
                    if ($product->isBatchManaged()) {
                        $batches = $product->batches()
                            ->where('quantity', '>', 0)
                            ->orderBy('expiry_date')
                            ->get();

                        foreach ($batches as $batch) {
                            if ($quantityRemaining <= 0) break;

                            $quantityToTake = min($batch->quantity, $quantityRemaining);

                            Stock::create([
                                'product_id' => $product->id,
                                'batch_id' => $batch->id,
                                'type' => 'out',
                                'quantity' => $quantityToTake,
                                'user_id' => Auth::id(),
                                'notes' => 'Order ' . $order->formatted_order_id . ' accepted',
                                'stock_updated_at' => now()
                            ]);

                            $batch->quantity -= $quantityToTake;
                            $batch->save();

                            if ($batch->quantity <= 0) {
                                $batch->delete();
                            }

                            $quantityRemaining -= $quantityToTake;
                        }
                    } else {
                        Stock::create([
                            'product_id' => $product->id,
                            'batch_id' => null,
                            'type' => 'out',
                            'quantity' => $detail->quantity,
                            'user_id' => Auth::id(),
                            'notes' => 'Order ' . $order->formatted_order_id . ' accepted',
                            'stock_updated_at' => now()
                        ]);
                    }

                    $product->update(['stock_updated_at' => now()]);
                }

                // Update order status
                $order->status = self::STATUS_PROCESSING;
                $order->status_updated_at = now();
                $order->save();

                // HANDLE DELIVERY CREATION BASED ON MULTI-ADDRESS STATUS
                if ($order->is_multi_address) {
                    // For multi-address orders, create a delivery for each unique address
                    // and link the OrderItemDelivery records to these deliveries
                    $orderItemDeliveries = \App\Models\OrderItemDelivery::with(['address', 'orderDetail'])
                        ->whereHas('orderDetail', function ($query) use ($order) {
                            $query->where('order_id', $order->id);
                        })
                        ->get();

                    if ($orderItemDeliveries->isEmpty()) {
                        throw new \Exception("No delivery addresses found for this multi-address order");
                    }

                    // Group by address_id to create one delivery per unique address
                    $deliveriesByAddress = [];

                    foreach ($orderItemDeliveries as $itemDelivery) {
                        $addressId = $itemDelivery->address_id;

                        // Create a delivery for this address if not already created
                        if (!isset($deliveriesByAddress[$addressId])) {
                            $delivery = Delivery::create([
                                'order_id' => $order->id,
                                'address_id' => $addressId,
                                'status' => 'pending',
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);

                            $deliveriesByAddress[$addressId] = $delivery;
                        }

                        // Update the OrderItemDelivery with the new delivery_id
                        $itemDelivery->update([
                            'delivery_id' => $deliveriesByAddress[$addressId]->id
                        ]);
                    }
                } else {
                    // For regular single-address orders, create one delivery for the entire order
                    // Get address information from the order details
                    $orderDetail = $order->orderDetails->first();
                    $addressId = null;

                    // Try to find an address for the retailer
                    if ($order->user && $order->user->retailerProfile && $order->user->retailerProfile->defaultAddress) {
                        $addressId = $order->user->retailerProfile->defaultAddress->id;
                    } else {
                        // You may need to handle this case differently if no default address is found
                        Log::warning('No default address found for user ID: ' . $order->user_id . ' on order: ' . $order->id);
                    }
                    $delivery = Delivery::create([
                        'order_id' => $order->id,
                        'address_id' => $addressId, // Use the found address ID or null
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                // Create payment record
                Payment::create([
                    'order_id' => $order->id,
                    'distributor_id' => $order->distributor_id,
                    'payment_status' => 'pending',
                ]);

                // Send notification
                app(NotificationService::class)->orderStatusChanged(
                    $order->id,
                    'processing',
                    $order->user_id,
                    $order->distributor_id
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Order accepted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Reject order: capture a reason, update order status and timestamp.
    public function rejectOrder(Request $request, Order $order)
    {
        if ($order->status !== self::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Only pending orders can be rejected.');
        }

        $data = $request->validate([
            'reject_reason' => 'required|string|max:255',
        ]);

        $order->update([
            'status' => self::STATUS_REJECTED,
            'reject_reason' => $data['reject_reason'],
            'status_updated_at' => now()
        ]);

        app(NotificationService::class)->orderStatusChanged(
            $order->id,
            'rejected',
            $order->user_id,
            $order->distributor_id,
            $data['reject_reason']
        );
        return redirect()->back()->with('success', 'Order rejected successfully.');
    }


    public function getOrderDetail(Order $order)
    {
        $order->load(['orderDetails.product.batches']); // Load product and batches for stock info

        $orderDetails = $order->orderDetails->map(function ($detail) {
            $product = $detail->product;

            // Calculate stock left
            $stockLeft = $product->isBatchManaged()
                ? $product->batches()->sum('quantity') // Sum of batch quantities for batch-managed products
                : Stock::where('product_id', $product->id)
                ->where('type', 'in')
                ->sum('quantity') - Stock::where('product_id', $product->id)
                ->where('type', 'out')
                ->sum('quantity'); // Stock calculation for non-batch products

            return [
                'id' => $detail->id,
                'quantity' => $detail->quantity,
                'product' => [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'isBatchManaged' => $product->isBatchManaged(),
                    'stockLeft' => $stockLeft, // Include stock left
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'orderDetails' => $orderDetails,
        ]);
    }

    public function getOrderDetails($id)
    {
        $order = Order::with([
            'user.retailer_profile',
            'orderDetails.product',
            'deliveries.address',
            'deliveries.itemDeliveries.orderDetail.product'
        ])->findOrFail($id);

        $html = view('distributors.orders.order-details-content', [
            'orderDetails' => $order->orderDetails,
            'retailer' => $order->user,
            'storageBaseUrl' => asset('storage')
        ])->render();

        return response()->json(['html' => $html]);
    }

    public function toggleOrderAcceptance(Request $request)
    {
        try {
            $user = Auth::user();
            $distributor = $user->distributor;

            if (!$distributor) {
                return response()->json(['success' => false, 'message' => 'Distributor profile not found'], 404);
            }

            $distributor->accepting_orders = $request->accepting_orders;
            $distributor->save();

            return response()->json([
                'success' => true,
                'accepting_orders' => $distributor->accepting_orders
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to toggle order acceptance: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred'], 500);
        }
    }


    public function editOrderQuantity(Request $request, Order $order)
    {
        if (!in_array($order->status, [self::STATUS_PENDING, self::STATUS_PROCESSING])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or processing orders can be edited.'
            ], 400);
        }

        $data = $request->validate([
            'order_details' => 'required|array',
            'order_details.*.id' => 'required|exists:order_details,id',
            'order_details.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $updatedOrderDetails = [];

            DB::transaction(function () use ($order, $data, &$updatedOrderDetails) {
                foreach ($data['order_details'] as $detail) {
                    $orderDetail = $order->orderDetails()->find($detail['id']);
                    $product = $orderDetail->product;

                    // Calculate the difference in quantity
                    $quantityDifference = $detail['quantity'] - $orderDetail->quantity;

                    // Recalculate discount
                    $discount = \App\Models\Discount::where('distributor_id', $product->distributor_id)
                        ->where('is_active', true)
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->whereHas('products', function ($query) use ($product) {
                            $query->where('product_id', $product->id);
                        })
                        ->first();

                    $discountAmount = 0;
                    if ($discount) {
                        if ($discount->type === 'percentage') {
                            $discountAmount = $discount->calculatePercentageDiscount($product->price) * $detail['quantity'];
                        } elseif ($discount->type === 'freebie') {
                            $freeItems = $discount->calculateFreeItems($detail['quantity']);
                            $discountAmount = $freeItems * $product->price;
                        }
                    }

                    // Update the order detail quantity, subtotal, and discount
                    $newSubtotal = ($product->price * $detail['quantity']) - $discountAmount;
                    $orderDetail->update([
                        'quantity' => $detail['quantity'],
                        'subtotal' => $newSubtotal,
                        'discount_amount' => $discountAmount,
                    ]);

                    // Adjust stock based on the quantity difference
                    if ($order->status === self::STATUS_PROCESSING) {
                        if ($product->isBatchManaged()) {
                            $this->adjustBatchStock($product, $quantityDifference, $order->id);
                        } else {
                            $this->adjustRegularStock($product, $quantityDifference, $order->id);
                        }
                    }

                    $updatedOrderDetails[] = [
                        'id' => $orderDetail->id,
                        'quantity' => $orderDetail->quantity,
                        'price' => (float) $product->price,
                        'subtotal' => $newSubtotal,
                        'discount_amount' => $discountAmount,
                    ];
                }

                // Update the order's total amount
                $order->update([
                    'total_amount' => $order->orderDetails->sum('subtotal'),
                    'status_updated_at' => now(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Order quantities updated successfully.',
                'updatedOrderDetails' => $updatedOrderDetails,
                'totalAmount' => $order->total_amount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function adjustBatchStock($product, $quantityDifference, $orderId)
    {
        if ($quantityDifference > 0) {
            // Stock out (reduce stock)
            $batches = $product->batches()
                ->where('quantity', '>', 0)
                ->orderBy('expiry_date')
                ->get();

            $remainingQuantity = $quantityDifference;

            foreach ($batches as $batch) {
                if ($remainingQuantity <= 0) break;

                $quantityToDeduct = min($batch->quantity, $remainingQuantity);

                // Create stock-out record
                Stock::create([
                    'product_id' => $product->id,
                    'batch_id' => $batch->id,
                    'type' => 'out',
                    'quantity' => $quantityToDeduct,
                    'user_id' => Auth::id(),
                    'notes' => "Order #{$orderId} updated",
                    'stock_updated_at' => now(),
                ]);

                // Update batch quantity
                $batch->quantity -= $quantityToDeduct;
                $batch->save();

                $remainingQuantity -= $quantityToDeduct;
            }
        } elseif ($quantityDifference < 0) {
            // Stock in (increase stock)
            $remainingQuantity = abs($quantityDifference);

            // Add stock back to the appropriate batches
            $batches = $product->batches()
                ->orderBy('expiry_date') // Add to the earliest expiry date first
                ->get();

            foreach ($batches as $batch) {
                if ($remainingQuantity <= 0) break;

                // Add stock to the batch
                $batch->quantity += $remainingQuantity;

                // Create stock-in record
                Stock::create([
                    'product_id' => $product->id,
                    'batch_id' => $batch->id,
                    'type' => 'in',
                    'quantity' => $remainingQuantity,
                    'user_id' => Auth::id(),
                    'notes' => "Order #{$orderId} updated",
                    'stock_updated_at' => now(),
                ]);

                $batch->save();
                $remainingQuantity = 0; // All stock has been added back
            }

            // If no batches exist, create a new batch
            if ($remainingQuantity > 0) {
                $newBatch = $product->batches()->create([
                    'batch_number' => 'new-' . uniqid(),
                    'quantity' => $remainingQuantity,
                    'expiry_date' => now()->addMonths(6), // Default expiry date
                    'received_at' => now(),
                ]);

                Stock::create([
                    'product_id' => $product->id,
                    'batch_id' => $newBatch->id,
                    'type' => 'in',
                    'quantity' => $remainingQuantity,
                    'user_id' => Auth::id(),
                    'notes' => "Order #{$orderId} updated",
                    'stock_updated_at' => now(),
                ]);
            }
        }
    }

    private function adjustRegularStock($product, $quantityDifference, $orderId)
    {
        if ($quantityDifference > 0) {
            // Stock out (reduce stock)
            Stock::create([
                'product_id' => $product->id,
                'batch_id' => null,
                'type' => 'out',
                'quantity' => $quantityDifference,
                'user_id' => Auth::id(),
                'notes' => "Order #{$orderId} updated",
                'stock_updated_at' => now(),
            ]);
        } elseif ($quantityDifference < 0) {
            // Stock in (increase stock)
            Stock::create([
                'product_id' => $product->id,
                'batch_id' => null,
                'type' => 'in',
                'quantity' => abs($quantityDifference),
                'user_id' => Auth::id(),
                'notes' => "Order #{$orderId} updated",
                'stock_updated_at' => now(),
            ]);
        }
    }


    public function history(Request $request)
    {
        $distributorId = Auth::user()->distributor->id;

        // Fetch completed and delivered orders
        $orders = Order::where('distributor_id', $distributorId)
            ->whereIn('status', ['completed', 'delivered']) // Filter by completed and delivered statuses
            ->when($request->search, function ($query) use ($request) {
                $query->where('order_id', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $request->search . '%');
                    });
            })
            ->with(['user', 'orderDetails.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('distributors.orders.history', compact('orders'));
    }


    public function getOrderDeliveries(Order $order)
    {
        $isMultiAddress = $order->is_multi_address;

        if ($isMultiAddress) {
            // For multi-address orders, get all delivery locations
            if ($order->status === 'pending') {
                // For pending orders, get from order_item_deliveries
                $deliveries = [];

                // Get distinct addresses
                $orderItemDeliveries = \App\Models\OrderItemDelivery::whereHas('orderDetail', function ($query) use ($order) {
                    $query->where('order_id', $order->id);
                })
                    ->with(['address', 'orderDetail.product'])
                    ->get()
                    ->groupBy('address_id');

                foreach ($orderItemDeliveries as $addressId => $items) {
                    $address = $items->first()->address;

                    // No need to manually lookup barangay name, use the accessor
                    // The accessor will be triggered when address is converted to array/JSON

                    $productItems = [];
                    foreach ($items as $item) {
                        if ($item->orderDetail && $item->orderDetail->product) {
                            $productItems[] = [
                                'product_id' => $item->orderDetail->product_id,
                                'product_name' => $item->orderDetail->product->product_name,
                                'quantity' => $item->quantity
                            ];
                        }
                    }

                    $deliveries[] = [
                        'address' => [
                            'id' => $address->id,
                            'barangay' => $address->barangay,
                            'barangay_name' => $address->barangay_name, // Explicitly call the accessor
                            'street' => $address->street,
                        ],
                        'status' => 'pending',
                        'items' => $productItems
                    ];
                }
            } else {
                // For orders in processing/other status, get from deliveries table
                $deliveries = $order->deliveries()->with(['address', 'orderItemDeliveries.orderDetail.product'])->get()
                    ->map(function ($delivery) {
                        // No need to manually lookup the barangay name

                        $items = $delivery->orderItemDeliveries->map(function ($itemDelivery) {
                            return [
                                'product_id' => $itemDelivery->orderDetail->product_id,
                                'product_name' => $itemDelivery->orderDetail->product->product_name,
                                'quantity' => $itemDelivery->quantity
                            ];
                        })->toArray();

                        return [
                            'id' => $delivery->id,
                            'address' => $delivery->address, // Address model already has barangay_name accessor
                            'status' => $delivery->status,
                            'items' => $items
                        ];
                    })->toArray();
            }
        } else {
            // For regular orders
            $deliveries = $order->deliveries()->with(['address'])->get()->map(function ($delivery) {
                // No need to manually lookup the barangay name

                return [
                    'id' => $delivery->id,
                    'address' => $delivery->address, // Address model already has barangay_name accessor
                    'status' => $delivery->status,
                    'items' => []
                ];
            })->toArray();
        }

        return response()->json([
            'success' => true,
            'is_multi_address' => $isMultiAddress,
            'deliveries' => $deliveries
        ]);
    }
}
