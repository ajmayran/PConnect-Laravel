<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Order;
use App\Models\Stock;
use App\Models\Payment;
use App\Models\Product;
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
            // For pending orders, show oldest first (first come, first served)
            $query = $query->oldest();
        } else if ($status === self::STATUS_PROCESSING) {
            // For processing orders, show latest status_updated_at first
            $query = $query->orderBy('status_updated_at', 'desc');
        } else {
            // For other statuses (rejected, etc.), show latest created first
            $query = $query->latest();
        }

        // Apply search
        if ($search) {
            $query->where(function ($query) use ($search) {
                // Search in order_id
                $query->where('order_id', 'like', "%{$search}%")
                    // Join and search in user table
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
            return redirect()->back()->with('error', 'Only pending orders can be accepted.');
        }

        DB::transaction(function () use ($order) {
            // Update each product quantity based on the order details
            foreach ($order->orderDetails as $detail) {
                $product = $detail->product;

                Stock::create([
                    'product_id' => $product->id,
                    'batch_id' => null, // For batch-managed products, you might want to implement batch selection logic
                    'type' => 'out',
                    'quantity' => $detail->quantity,
                    'user_id' => Auth::id(),
                    'notes' => 'Order #' . $order->id . ' accepted',
                    'stock_updated_at' => now()
                ]);
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
                'distributor_id' => $order->distributor_id,
                'payment_status'  => 'unpaid',

            ]);

            app(NotificationService::class)->orderStatusChanged(
                $order->id,
                'processing',
                $order->user_id,
                $order->distributor_id
            );
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

        app(NotificationService::class)->orderStatusChanged(
            $order->id,
            'rejected',
            $order->user_id,
            $order->distributor_id,
            $data['reject_reason']
        );
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
}
