<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Trucks;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class ExchangeController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $distributorId = Auth::user()->distributor->id;
        $status = $request->input('status', 'pending');
        $search = $request->input('search');

        // Base query for exchange deliveries
        $query = Delivery::with(['order.user', 'returnRequest.items.orderDetail.product'])
            ->whereNotNull('exchange_for_return_id')
            ->whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            });

        // Filter by status if provided
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Apply search if provided
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereHas('order', function ($query) use ($search) {
                    $query->where('order_id', 'like', "%{$search}%");
                })->orWhereHas('order.user', function ($query) use ($search) {
                    $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                });
            });
        }

        $exchanges = $query->latest()->paginate(10);

        // Get available trucks for assigning deliveries
        $availableTrucks = Trucks::where('distributor_id', $distributorId)
            ->where('status', 'available')
            ->get();

        return view('distributors.exchanges.index', compact('exchanges', 'availableTrucks', 'status'));
    }

    public function assignTruck(Request $request, Delivery $delivery)
    {
        $request->validate([
            'truck_id' => 'required|exists:trucks,id',
        ]);

        $distributorId = Auth::user()->distributor->id;

        // Check if delivery belongs to this distributor
        if (!$delivery->order || $delivery->order->distributor_id != $distributorId) {
            return redirect()->back()->with('error', 'Unauthorized access to this exchange delivery');
        }

        // Check if delivery is a valid exchange delivery
        if (!$delivery->exchange_for_return_id) {
            return redirect()->back()->with('error', 'This is not a valid exchange delivery');
        }

        try {
            DB::beginTransaction();

            // Get the truck
            $truck = Trucks::findOrFail($request->truck_id);

            // Update delivery status to in_transit
            $delivery->update(['status' => 'in_transit']);

            // Update the truck status to on_delivery
            $truck->update(['status' => 'on_delivery']);

            // Attach truck to delivery
            $truck->deliveries()->attach($delivery->id);

            // Send notification to retailer
            $this->notificationService->create(
                $delivery->order->user_id,
                'exchange_in_transit',
                [
                    'title' => 'Exchange Items in Transit',
                    'message' => "Your exchange items for order #{$delivery->order->formatted_order_id} are now in transit.",
                    'delivery_id' => $delivery->id,
                    'order_id' => $delivery->order_id,
                    'recipient_type' => 'retailer'
                ],
                $delivery->order_id
            );

            DB::commit();

            return redirect()->back()->with('success', 'Truck assigned to exchange delivery successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign truck to exchange: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to assign truck: ' . $e->getMessage());
        }
    }

    // New method to handle out for delivery status
    public function markOutForDelivery(Request $request, Delivery $delivery)
    {
        $distributorId = Auth::user()->distributor->id;

        // Validate the request
        $request->validate([
            'estimated_delivery' => 'required|date|after_or_equal:today',
        ]);

        // Check if delivery belongs to this distributor
        if (!$delivery->order || $delivery->order->distributor_id != $distributorId) {
            return redirect()->back()->with('error', 'Unauthorized access to this exchange delivery');
        }

        try {
            DB::beginTransaction();

            // Update delivery status
            $delivery->update([
                'status' => 'out_for_delivery',
                'estimated_delivery' => $request->estimated_delivery,
                'updated_at' => now()
            ]);

            // Send notification to retailer
            $this->notificationService->create(
                $delivery->order->user_id,
                'exchange_out_for_delivery',
                [
                    'title' => 'Exchange Items Out for Delivery',
                    'message' => "Your exchange items for order #{$delivery->order->formatted_order_id} are out for delivery.",
                    'delivery_id' => $delivery->id,
                    'order_id' => $delivery->order_id,
                    'recipient_type' => 'retailer',
                    'estimated_delivery' => $request->estimated_delivery
                ],
                $delivery->order_id
            );

            DB::commit();

            return redirect()->back()->with('success', 'Exchange delivery marked as out for delivery successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark exchange as out for delivery: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update delivery status: ' . $e->getMessage());
        }
    }

    public function markDelivered(Request $request, Delivery $delivery)
    {
        $distributorId = Auth::user()->distributor->id;

        // Check if delivery belongs to this distributor
        if (!$delivery->order || $delivery->order->distributor_id != $distributorId) {
            return redirect()->back()->with('error', 'Unauthorized access to this exchange delivery');
        }

        try {
            DB::beginTransaction();

            // Update delivery status
            $delivery->update([
                'status' => 'delivered',
                'updated_at' => now()
            ]);

            // Get the truck if assigned
            $truck = $delivery->trucks()->first();
            if ($truck) {
                // Check if there are any remaining active deliveries for this truck
                $activeDeliveriesCount = $truck->deliveries()
                    ->whereIn('status', ['pending', 'in_transit', 'out_for_delivery'])
                    ->count();

                // If no more active deliveries, mark truck as available
                if ($activeDeliveriesCount === 0) {
                    $truck->update(['status' => 'available']);

                    // Detach delivered deliveries
                    $completedDeliveries = $truck->deliveries()
                        ->where('deliveries.status', 'delivered')
                        ->pluck('deliveries.id')
                        ->toArray();

                    $truck->deliveries()->detach($completedDeliveries);
                }
            }

            // Process exchange items if there's a return request
            if ($delivery->returnRequest && $delivery->returnRequest->items) {
                foreach ($delivery->returnRequest->items as $item) {
                    $product = $item->orderDetail->product;

                    if (!$product) {
                        Log::warning('Product not found for return item', ['item_id' => $item->id]);
                        continue;
                    }

                    // Add stock based on product type (batch managed or regular)
                    if ($product->isBatchManaged()) {
                        $this->adjustBatchStockForExchange($product, $item->quantity, $delivery->returnRequest);
                    } else {
                        // For regular products, just create a stock record
                        \App\Models\Stock::create([
                            'product_id' => $product->id,
                            'batch_id' => null,
                            'type' => 'in',
                            'quantity' => $item->quantity,
                            'user_id' => Auth::id(),
                            'notes' => "Exchange for Return Request #{$delivery->returnRequest->id} completed",
                            'stock_updated_at' => now()
                        ]);

                        // Update product's stock_updated_at
                        $product->update(['stock_updated_at' => now()]);
                    }
                }
            }

            // Update the return request status to completed
            if ($delivery->returnRequest) {
                $delivery->returnRequest->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
            }

            // Send notification to retailer
            $this->notificationService->create(
                $delivery->order->user_id,
                'exchange_delivered',
                [
                    'title' => 'Exchange Items Delivered',
                    'message' => "Your exchange items for order #{$delivery->order->formatted_order_id} have been delivered.",
                    'delivery_id' => $delivery->id,
                    'order_id' => $delivery->order_id,
                    'recipient_type' => 'retailer'
                ],
                $delivery->order_id
            );

            if ($delivery->order) {
                $delivery->order->update([
                    'status' => 'returned',
                    'status_updated_at' => now()
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Exchange delivery marked as delivered successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark exchange as delivered: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to complete delivery: ' . $e->getMessage());
        }
    }

    public function getExchangeDetails($id)
    {
        try {
            $delivery = Delivery::with([
                'order.user',
                'returnRequest.items.orderDetail.product'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'delivery' => $delivery,
                'return' => $delivery->returnRequest,
                'items' => $delivery->returnRequest ? $delivery->returnRequest->items : [],
                'customer' => $delivery->order->user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load exchange details: ' . $e->getMessage()
            ], 500);
        }
    }

    private function adjustBatchStockForExchange($product, $quantity, $returnRequest)
    {
        $remainingQuantity = $quantity;

        // Try to add stock to existing batches first
        $batches = $product->batches()
            ->orderBy('expiry_date')
            ->get();

        foreach ($batches as $batch) {
            if ($remainingQuantity <= 0) break;

            // Create stock record
            \App\Models\Stock::create([
                'product_id' => $product->id,
                'batch_id' => $batch->id,
                'type' => 'in',
                'quantity' => $remainingQuantity,
                'user_id' => Auth::id(),
                'notes' => "Exchange for Return Request #{$returnRequest->id} completed",
                'stock_updated_at' => now()
            ]);

            // Update batch quantity
            $batch->increment('quantity', $remainingQuantity);
            $remainingQuantity = 0;
        }

        // If no batches exist or all were full, create a new batch
        if ($remainingQuantity > 0) {
            $newBatch = $product->batches()->create([
                'batch_number' => 'exchange-' . uniqid(),
                'quantity' => $remainingQuantity,
                'expiry_date' => now()->addMonths(6), // Default expiry of 6 months
                'received_at' => now(),
                'notes' => "Created from Exchange #{$returnRequest->id}"
            ]);

            \App\Models\Stock::create([
                'product_id' => $product->id,
                'batch_id' => $newBatch->id,
                'type' => 'in',
                'quantity' => $remainingQuantity,
                'user_id' => Auth::id(),
                'notes' => "Exchange for Return Request #{$returnRequest->id} completed",
                'stock_updated_at' => now()
            ]);
        }

        // Update product's stock_updated_at timestamp
        $product->update(['stock_updated_at' => now()]);
    }
}
