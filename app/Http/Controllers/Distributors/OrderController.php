<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Delivery;
use App\Models\Product;
use Illuminate\Support\Facades\DB;


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

        // Base query without search filter
        $query = Order::with(['orderDetails.product', 'user.retailerProfile'])
            ->where('distributor_id', $distributorId)
            ->where('status', $status);

        // For pending orders, show oldest first
        if ($status === self::STATUS_PENDING) {
            $query = $query->oldest();
        } else {
            $query = $query->latest();
        }

        // Get all orders first
        $allOrders = $query->get();

        // Then filter by search if provided
        if ($search) {
            $orders = $allOrders->filter(function ($order) use ($search) {
                // Check formatted ID
                if (stripos($order->formatted_order_id, $search) !== false) {
                    return true;
                }

                // Check retailer name
                $retailerName = $order->user->first_name . ' ' . $order->user->last_name;
                if (stripos($retailerName, $search) !== false) {
                    return true;
                }

                return false;
            });
        } else {
            $orders = $allOrders;
        }

        return view('distributors.orders.index', compact('orders'));
    }

    public function acceptOrder(Request $request, Order $order)
    {

        if ($order->status !== self::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Only pending orders can be accepted.');
        }

        DB::transaction(function () use ($order) {
            // Update each product quantity based on the order details
            foreach ($order->orderDetails as $detail) {
                $product = $detail->product;
                // Deduct the ordered quantity (ensure your business logic prevents negative inventory)
                $product->stock_quantity = max(0, $product->stock_quantity - $detail->quantity);
                $product->save();
            }
            $order->status = self::STATUS_PROCESSING;
            $order->status_updated_at = now();
            $order->save();

            $trackingNumber = 'TRK-' . strtoupper(uniqid());
            // Generate a new delivery record
            Delivery::create([
                'order_id'      => $order->id,
                'tracking_number'  => $trackingNumber,
                'status'        => 'pending',
                'created_at' => now(),
                'updated_at' => now()

                // Add additional fields as per your Delivery model
            ]);

            Payment::create([
                'order_id'        => $order->id,
                'distributor_id' => $order->distributor_id, // Assumes distributor_id is stored on the Order model
                'payment_status'  => 'unpaid',

            ]);
        });


        return redirect()->back()->with('success', 'Order accepted successfully.');
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

        return redirect()->back()->with('success', 'Order rejected successfully.');
    }

    public function getOrderDetails($id)
    {
        $order = Order::with(['user.retailer_profile', 'orderDetails.product'])->findOrFail($id);
        $html = view('distributors.orders.order-details-content', [
            'orderDetails'   => $order->orderDetails,
            'retailer'       => $order->user,
            'storageBaseUrl' => asset('storage')
        ])->render();

        return response()->json(['html' => $html]);
    }
}
