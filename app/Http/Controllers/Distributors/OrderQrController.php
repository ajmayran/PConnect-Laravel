<?php

namespace App\Http\Controllers\Distributors;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Earning;
use App\Models\Payment;
use App\Models\Delivery;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderQrController extends Controller
{
    public function showQrCode(Order $order)
    {
        if (empty($order->qr_token)) {
            $order->qr_token = Str::random(32);
            $order->save();
        }

        $verificationUrl = route('distributors.orders.verify', $order->qr_token);

        return view('distributors.orders.qrcode', compact('order', 'verificationUrl'));
    }

    public function verifyOrder($token)
    {
        $order = Order::where('qr_token', $token)->firstOrFail();
        $payment = Payment::where('order_id', $order->id)->first();

        // Determine what options are available based on order status
        $availableActions = [];

        if ($payment && $payment->payment_status === 'unpaid') {
            $availableActions[] = 'confirm_payment';
        }

        if ($order->status === 'processing' || $order->status === 'shipped') {
            $availableActions[] = 'confirm_delivery';
        }

        return view('distributors.orders.verify', [
            'order' => $order,
            'payment' => $payment,
            'availableActions' => $availableActions
        ]);
    }

    public function processAction(Request $request, $token)
    {
        $order = Order::where('qr_token', $token)->firstOrFail();
    
        try {
            DB::transaction(function () use ($request, $order) {
                $action = $request->action;
    
                if ($action === 'confirm_payment') {
                    $payment = Payment::where('order_id', $order->id)->first();
    
                    if ($payment && $payment->payment_status === 'unpaid') {
                        // Update payment status
                        $payment->update([
                            'payment_status' => 'paid',
                            'payment_note' => 'Payment confirmed via QR code',
                            'paid_at' => now()
                        ]);
    
                        // Create earning record
                        $totalAmount = $order->orderDetails->sum('subtotal');
                        Earning::create([
                            'payment_id' => $payment->id,
                            'distributor_id' => $payment->distributor_id,
                            'amount' => $totalAmount
                        ]);
    
                        // Update order status if needed
                        if ($order->status === 'pending') {
                            $order->update([
                                'status' => 'processing',
                                'status_updated_at' => now()
                            ]);
                        }
                    }
                }
    
                if ($action === 'confirm_delivery') {
                    // For multi-address orders, handle the specific delivery
                    if ($order->is_multi_address) {
                        // We need to know which delivery this applies to
                        $deliveryId = $request->delivery_id;
                        if (!$deliveryId) {
                            throw new \Exception("Delivery ID is required for multi-address orders");
                        }
    
                        $delivery = Delivery::find($deliveryId);
                        if (!$delivery || $delivery->order_id != $order->id) {
                            throw new \Exception("Invalid delivery for this order");
                        }
    
                        // Update delivery status
                        $delivery->update([
                            'status' => 'delivered',
                            'payment_status' => $request->payment_status ?? 'paid',
                            'payment_note' => $request->payment_note ?? 'Delivery confirmed via QR code'
                        ]);
    
                        // Create or update payment record for this delivery
                        $payment = Payment::firstOrNew([
                            'order_id' => $order->id,
                            'delivery_id' => $delivery->id
                        ]);
                        
                        $payment->distributor_id = $order->distributor_id;
                        $payment->payment_status = $request->payment_status ?? 'paid';
                        $payment->payment_note = $request->payment_note ?? 'Payment confirmed via QR code';
                        
                        if ($request->payment_status === 'paid') {
                            $payment->paid_at = now();
                        }
                        
                        $payment->save();
    
                        // Create earning record if payment is marked as paid
                        if ($request->payment_status === 'paid') {
                            // Calculate total from OrderItemDelivery records for this delivery
                            $deliveryTotal = 0;
                            $deliveryItems = \App\Models\OrderItemDelivery::where('delivery_id', $delivery->id)
                                ->with('orderDetail')
                                ->get();
                                
                            foreach ($deliveryItems as $item) {
                                if ($item->orderDetail) {
                                    $itemPrice = $item->orderDetail->price;
                                    $itemQuantity = $item->quantity;
                                    $deliveryTotal += ($itemPrice * $itemQuantity);
                                }
                            }
    
                            Earning::create([
                                'payment_id' => $payment->id,
                                'distributor_id' => $order->distributor_id,
                                'amount' => $deliveryTotal
                            ]);
                        }
    
                        // Check if all deliveries for this order have been delivered
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
                            
                            // Update order status to completed
                            $order->update([
                                'status' => 'completed',
                                'status_updated_at' => now(),
                                'completed_at' => now()
                            ]);
                            
                            // Add notification for completed order
                            app(NotificationService::class)->orderStatusChanged(
                                $order->id,
                                'completed',
                                $order->user_id,
                                $order->distributor_id
                            );
                        }
                    } else {
                        // For regular orders, update order status directly
                        $order->update([
                            'status' => 'completed',
                            'status_updated_at' => now(),
                            'delivery_date' => now()
                        ]);
                        
                        // Update delivery if it exists
                        if ($order->delivery) {
                            $order->delivery->update([
                                'status' => 'delivered',
                                'payment_status' => $request->payment_status ?? 'paid',
                                'payment_note' => $request->payment_note ?? 'Delivery confirmed via QR code'
                            ]);
                        }
                        
                        // Create or update payment record
                        $payment = Payment::firstOrNew(['order_id' => $order->id]);
                        $payment->distributor_id = $order->distributor_id;
                        $payment->payment_status = $request->payment_status ?? 'paid';
                        $payment->payment_note = $request->payment_note ?? 'Payment confirmed via QR code';
                        
                        if ($request->payment_status === 'paid') {
                            $payment->paid_at = now();
                        }
                        
                        $payment->save();
                        
                        // Create earning record if payment is marked as paid
                        if ($request->payment_status === 'paid') {
                            $totalAmount = $order->orderDetails->sum('subtotal');
                            Earning::create([
                                'payment_id' => $payment->id,
                                'distributor_id' => $order->distributor_id,
                                'amount' => $totalAmount
                            ]);
                        }
                        
                        // Add notification
                        app(NotificationService::class)->orderStatusChanged(
                            $order->id,
                            'completed',
                            $order->user_id,
                            $order->distributor_id
                        );
                    }
                }
            });
    
            return redirect()->route('order.verify', $token)
                ->with('success', 'Action completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order action failed: ' . $e->getMessage());
    
            return redirect()->route('order.verify', $token)
                ->with('error', 'Action failed: ' . $e->getMessage());
        }
    }

    public function getProcessingOrders()
    {
        $distributorId = Auth::user()->distributor->id;

        $orders = Order::with(['user', 'orderDetails.product'])
            ->where('status', 'processing')
            ->where('distributor_id', $distributorId)
            ->get();

        // Explicitly add the formatted_order_id to each order
        $orders = $orders->map(function ($order) {
            $orderArray = $order->toArray();
            $orderArray['formatted_order_id'] = $order->formatted_order_id;
            return $orderArray;
        });

        if ($orders->isEmpty()) {
            Log::info('No processing orders found for distributor ' . $distributorId);
        } else {
            Log::info('Found ' . $orders->count() . ' processing orders for distributor ' . $distributorId);
        }

        return response()->json([
            'orders' => $orders
        ]);
    }

    public function generateBatchQrCodes(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        $orderIds = $request->order_ids;
        $distributorId = Auth::user()->distributor->id;

        // Get orders that belong to this distributor
        $orders = Order::with('user')
            ->whereIn('id', $orderIds)
            ->where('distributor_id', $distributorId)
            ->get();

        // Generate QR tokens if needed
        foreach ($orders as $order) {
            if (empty($order->qr_token)) {
                $order->qr_token = Str::random(32);
                $order->save();
            }
        }

        return view('distributors.orders.batch-qrcode', compact('orders'));
    }

    public function scanQrCode(Request $request, $delivery)
    {
        // If we're accessing the general scanner
        if ($delivery === 'general') {
            return view('distributors.delivery.scan-qr-general');
        }

        // Otherwise, find the specific delivery
        $delivery = Delivery::findOrFail($delivery);

        // Validate that the delivery belongs to the authenticated distributor
        if ($delivery->order->distributor_id !== Auth::user()->distributor->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('distributors.delivery.scan-qr-general', compact('delivery'));
    }


    public function processGeneralScan(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'qr_token' => 'required|string',
            'payment_status' => 'required|in:paid,unpaid',
            'delivery_id' => 'sometimes|exists:deliveries,id',
            'payment_note' => 'nullable|string|max:500',
        ]);

        // Find order by QR token
        $order = Order::where('qr_token', $validated['qr_token'])->first();

        if (!$order) {
            return back()->with('error', 'Invalid QR code or order not found.');
        }

        // Check if order belongs to authenticated distributor
        if ($order->distributor_id !== Auth::user()->distributor->id) {
            return back()->with('error', 'This order is not assigned to your distribution center.');
        }

        // Check if order status is "processing"
        if ($order->status !== 'processing') {
            return back()->with('error', 'This order cannot be processed. Only orders with "processing" status can be completed.');
        }

        // Get the delivery - either from the form or from the order
        $delivery = null;
        if (!empty($validated['delivery_id'])) {
            $delivery = Delivery::find($validated['delivery_id']);
        }

        if (!$delivery) {
            $delivery = $order->delivery;
        }

        if (!$delivery) {
            return back()->with('error', 'No delivery found for this order.');
        }

        // Check if delivery status is "out_for_delivery"
        if ($delivery->status !== 'out_for_delivery') {
            return back()->with('error', 'This delivery cannot be processed. Only deliveries with "out for delivery" status can be completed.');
        }

        // Process the delivery completion in a transaction
        DB::transaction(function () use ($order, $delivery, $validated) {
            // Update delivery status
            $delivery->update([
                'status' => 'delivered',
                'notes' => $validated['payment_note'] ?? null
            ]);

            $orderStatus = ($validated['payment_status'] === 'paid') ? 'completed' : 'delivered';
            $order->update([
                'status' => $orderStatus,
                'status_updated_at' => now(),
                'delivery_date' => now()
            ]);

            app(NotificationService::class)->orderStatusChanged(
                $order->id,
                $orderStatus,
                $order->user_id,
                $order->distributor_id
            );
            
            //  Update or create payment record
            $payment = Payment::firstOrNew(['order_id' => $order->id]);
            $payment->distributor_id = $order->distributor_id;
            $payment->payment_status = $validated['payment_status'];

            if ($validated['payment_status'] === 'paid') {
                $payment->paid_at = now();
                $payment->payment_note = $validated['payment_note'] ?? 'Payment confirmed upon delivery via QR code';
            } else {
                // For unpaid: only use default message if no note is provided
                $payment->payment_note = !empty($validated['payment_note']) ?
                    $validated['payment_note'] :
                    'Unpaid - Delivery completed';
            }

            $payment->save();

            //  If payment is marked as paid, create an earning record
            if ($validated['payment_status'] === 'paid') {
                $totalAmount = $order->orderDetails->sum('subtotal');
                Earning::create([
                    'payment_id' => $payment->id,
                    'distributor_id' => $payment->distributor_id,
                    'amount' => $totalAmount
                ]);
            }

            //  Update truck 
            $truck = $delivery->trucks()->first();
            if ($truck) {
                $activeDeliveriesCount = $truck->deliveries()
                    ->whereIn('status', ['pending', 'in_transit', 'out_for_delivery'])
                    ->count();

                if ($activeDeliveriesCount === 0) {
                    $truck->update(['status' => 'available']);

                    // Detach completed deliveries
                    $completedDeliveries = $truck->deliveries()
                        ->where('deliveries.status', 'delivered')
                        ->pluck('deliveries.id')
                        ->toArray();

                    $truck->deliveries()->detach($completedDeliveries);
                }
            }
        });

        return redirect()->route('distributors.delivery.index', ['status' => 'delivered'])
            ->with('success', 'Delivery completed successfully!');
    }


    public function verifyQrToken(Request $request)
    {
        $token = $request->token;

        // Find order by QR token
        $order = Order::where('qr_token', $token)
            ->with(['user', 'delivery', 'orderDetails'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code or order not found.'
            ], 404);
        }

        // Check if order belongs to authenticated distributor
        if ($order->distributor_id !== Auth::user()->distributor->id) {
            return response()->json([
                'success' => false,
                'message' => 'This order is not assigned to your distribution center.'
            ], 403);
        }
        // Check if order status is "processing"
        if ($order->status !== 'processing') {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be scanned. Only orders with "processing" status can be completed.'
            ], 400);
        }

        // Check if delivery exists
        $delivery = $order->delivery;
        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'No delivery found for this order.'
            ], 400);
        }

        // Check if delivery status is "out_for_delivery"
        if ($delivery->status !== 'out_for_delivery') {
            return response()->json([
                'success' => false,
                'message' => 'This delivery cannot be scanned. Only deliveries with "in_transit" status can be completed.'
            ], 400);
        }

        // Return order details for confirmation
        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'formatted_id' => $order->formatted_order_id,
                'retailer_name' => $order->user->first_name . ' ' . $order->user->last_name,
                'status' => $order->status,
                'delivery_status' => $delivery->status,
                'total_amount' => number_format($order->orderDetails->sum('subtotal'), 2),
                'delivery_id' => $delivery->id
            ],
            'delivery' => $delivery
        ]);
    }

    public function showGeneralQrScanner()
    {
        return view('distributors.delivery.scan-qr-general');
    }
}
