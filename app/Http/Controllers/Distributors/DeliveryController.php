<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Order;
use App\Models\Trucks;
use App\Models\Payment;
use App\Models\Earning;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{

    const STATUS_PENDING = 'pending';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    const STATUS_FAILED = 'failed';
    const STATUS_DELIVERED = 'delivered';

    public function index()
    {
        $distributorId = Auth::user()->distributor->id;
        $status = request('status', self::STATUS_PENDING);
        $search = request('search');

        // Build base query to include both regular and exchange deliveries
        $query = Delivery::with([
            'order',
            'order.user.retailerProfile',
            'order.orderDetails.product',
            'address',
            'returnRequest.items.orderDetail.product', // Add this relationship for exchange deliveries
            'orderItemDeliveries.address' // Add this to load the address relationships
        ])
            ->whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            });

        // Handle special "exchanges" filter
        if (request('view') === 'exchanges') {
            $query->where(function ($q) {
                $q->where('is_exchange_delivery', true)
                    ->orWhereNotNull('exchange_for_return_id');
            });
        }
        // Handle regular status filtering
        else if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Apply search if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($sq) use ($search) {
                        $sq->where('formatted_order_id', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($usq) use ($search) {
                                $usq->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                            });
                    });
            });
        }

        $deliveries = $query->latest()->paginate(10);
        $deliveries->appends(request()->query());

        // Get available trucks for assigning deliveries
        $availableTrucks = Trucks::where('distributor_id', $distributorId)
            ->where('status', 'available')
            ->get();

        return view('distributors.delivery.index', compact('deliveries', 'availableTrucks', 'status'));
    }

    public function markDelivered(Request $request, Delivery $delivery)
    {
        // Validate the request
        $validated = $request->validate([
            'payment_status' => ['required', 'string', Rule::in(['paid', 'unpaid'])],
            'payment_note' => ['nullable', 'string', 'max:500'],
        ]);
    
        // Check if the delivery is in a valid state to be marked as delivered
        if ($delivery->status !== 'out_for_delivery') {
            return back()->with('error', 'This delivery cannot be marked as delivered in its current state.');
        }
    
        $distributor = Auth::user()->distributor;
        $order = $delivery->order;
    
        DB::beginTransaction();
        try {
            // Update delivery status to delivered
            $delivery->update([
                'status' => 'delivered',
                'payment_status' => $validated['payment_status'],
                'payment_note' => $validated['payment_note'],
                'updated_at' => now()
            ]);
    
            // Different handling for multi-address vs regular orders
            if ($order->is_multi_address) {
                // MULTI-ADDRESS ORDER HANDLING
    
                // Calculate this delivery's portion of the total
                $deliveryItems = $delivery->orderItemDeliveries;
                $deliveryTotal = 0;
    
                foreach ($deliveryItems as $item) {
                    if ($item->orderDetail) {
                        $itemPrice = $item->orderDetail->price;
                        $itemQuantity = $item->quantity;
                        $deliveryTotal += ($itemPrice * $itemQuantity);
                    }
                }
    
                // Create a payment record specific to this delivery
                $payment = new Payment();
                $payment->order_id = $order->id;
                $payment->delivery_id = $delivery->id; // Link payment to this specific delivery
                $payment->distributor_id = $distributor->id;
                $payment->payment_status = $validated['payment_status'];
                $payment->payment_note = $validated['payment_note'];
                
                if ($validated['payment_status'] === 'paid') {
                    $payment->paid_at = now();
                }
                
                $payment->save();
    
                // Only create earnings for paid deliveries
                if ($validated['payment_status'] === 'paid') {
                    $earning = new Earning();
                    $earning->payment_id = $payment->id;
                    $earning->distributor_id = $distributor->id;
                    $earning->amount = $deliveryTotal; // Only count this delivery's portion
                    $earning->save();
                }
    
                // Check if all deliveries for this order are now delivered
                $allOrderDeliveries = Delivery::where('order_id', $order->id)->get();
                $allDelivered = $allOrderDeliveries->every(function ($del) {
                    return $del->status === 'delivered';
                });
    
                if ($allDelivered) {
                    // Count how many are paid vs total
                    $paidDeliveries = $allOrderDeliveries->filter(function ($del) {
                        return $del->payment_status === 'paid';
                    })->count();
                    
                    $totalDeliveries = $allOrderDeliveries->count();
                    
                    // Determine overall payment status
                    $orderPaymentStatus = 'unpaid';
                    if ($paidDeliveries === $totalDeliveries) {
                        $orderPaymentStatus = 'paid';
                    } else if ($paidDeliveries > 0) {
                        $orderPaymentStatus = 'partial';
                    }
                    
                    // Update order status
                    $order->update([
                        'status' => 'completed',
                        'payment_status' => $orderPaymentStatus,
                        'status_updated_at' => now()
                    ]);
                    
                    // Notify user about complete order
                    $statusMessage = "Your order {$order->formatted_order_id} has been fully delivered";
                    if ($orderPaymentStatus === 'paid') {
                        $statusMessage .= " and paid.";
                    } else if ($orderPaymentStatus === 'partial') {
                        $statusMessage .= " with partial payment.";
                    } else {
                        $statusMessage .= " but is pending payment.";
                    }
                    
                    app(NotificationService::class)->deliveryStatusChanged(
                        $delivery->id,
                        'delivered',
                        $order->user_id,
                        $statusMessage
                    );
                } else {
                    // Just notify about this specific delivery
                    app(NotificationService::class)->deliveryStatusChanged(
                        $delivery->id,
                        'delivered',
                        $order->user_id,
                        "Part of your order {$order->formatted_order_id} has been delivered."
                    );
                }
            } else {
                // REGULAR SINGLE-ADDRESS ORDER HANDLING
                
                // Find existing payment record and update it, or create a new one if none exists
                $payment = Payment::firstOrNew(
                    ['order_id' => $delivery->order_id],
                    ['distributor_id' => $distributor->id]
                );

                Log::info('Processing payment for regular order', [
                    'order_id' => $order->id,
                    'payment_status' => $validated['payment_status']
                ]);
                
                // Update payment details
                $payment->payment_status = $validated['payment_status'];
                $payment->payment_note = $validated['payment_note'];
                $payment->paid_at = $validated['payment_status'] === 'paid' ? now() : null;
                $payment->save();
                
                // Log the payment update
                Log::info('Payment record updated:', [
                    'order_id' => $delivery->order_id,
                    'payment_id' => $payment->id,
                    'payment_status' => $payment->payment_status
                ]);
                
                // If payment is marked as paid, create an earnings record
                if ($validated['payment_status'] === 'paid') {
                    // Check if an earning record already exists
                    $existingEarning = Earning::where('payment_id', $payment->id)->first();
                    
                    if (!$existingEarning) {
                        $earning = new Earning();
                        $earning->payment_id = $payment->id;
                        $earning->distributor_id = $distributor->id;
                        $earning->amount = $order->total;
                        $earning->save();

                    }
                }
                
                // Update the order status to completed
                $orderStatus = $validated['payment_status'] === 'paid' ? 'completed' : 'delivered';
                
                $order->update([
                    'status' => $orderStatus,
                    'payment_status' => $validated['payment_status'],
                    'status_updated_at' => now()
                ]);
                
                Log::info('Order updated:', [
                    'order_id' => $order->id,
                    'new_status' => $orderStatus,
                    'payment_status' => $validated['payment_status']
                ]);
                
                // Add notification for order status change
                app(NotificationService::class)->orderStatusChanged(
                    $order->id,
                    $orderStatus,
                    $order->user_id,
                    $order->distributor_id
                );
                
                // Add delivery status notification
                app(NotificationService::class)->deliveryStatusChanged(
                    $delivery->id,
                    'delivered',
                    $order->user_id
                );
            }
    
            // Check if this is the last delivery for the truck
            $truck = $delivery->trucks()->first();
            if ($truck) {
                $activeDeliveriesCount = $truck->deliveries()
                    ->whereIn('status', ['in_transit', 'out_for_delivery'])
                    ->count();
    
                if ($activeDeliveriesCount === 0) {
                    $truck->update(['status' => 'available']);
                }
            }
    
            DB::commit();
            return redirect()->back()->with('success', 'Delivery marked as delivered successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error marking delivery as delivered: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark delivery as delivered: ' . $e->getMessage());
        }
    }

    public function getDeliveryDetails($id)
    {
        $delivery = Delivery::findOrFail($id);
        $order = $delivery->order;

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found for this delivery'
            ]);
        }

        if (!$order->is_multi_address) {
            return response()->json([
                'success' => false,
                'message' => 'This is not a multi-address delivery'
            ]);
        }

        // Get all item deliveries for this delivery
        $orderItemDeliveries = \App\Models\OrderItemDelivery::where('delivery_id', $delivery->id)
            ->with(['address', 'orderDetail.product'])
            ->get()
            ->groupBy('address_id');

        $items = [];

        foreach ($orderItemDeliveries as $addressId => $deliveryItems) {
            // Get address details
            $address = $deliveryItems->first()->address;

            if (!$address) {
                continue;
            }

            // Create products array for this address
            $products = [];
            foreach ($deliveryItems as $item) {
                if ($item->orderDetail && $item->orderDetail->product) {
                    $products[] = [
                        'id' => $item->orderDetail->product_id,
                        'name' => $item->orderDetail->product->product_name,
                        'quantity' => $item->quantity,
                        'price' => $item->orderDetail->price,
                        'subtotal' => $item->orderDetail->price * $item->quantity
                    ];
                }
            }

            // Add this address entry to the items array
            $items[] = [
                'address' => [
                    'id' => $address->id,
                    'barangay' => $address->barangay,
                    'barangay_name' => $address->barangay_name,
                    'street' => $address->street,
                ],
                'products' => $products
            ];
        }

        return response()->json([
            'success' => true,
            'delivery_id' => $delivery->id,
            'order_id' => $order->id,
            'items' => $items
        ]);
    }
}
