<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Order;
use Illuminate\Http\Request;
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
        $orders = Order::with(['orderDetails.product', 'user.retailerProfile']) // 'user' is the retailer who ordered
            ->where('distributor_id', $distributorId)
            ->where('status', self::STATUS_PENDING)
            ->latest()
            ->get();

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
}
