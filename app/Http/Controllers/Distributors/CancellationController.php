<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class CancellationController extends Controller
{
    public function index(Request $request)
    {
        $distributorId = Auth::user()->distributor->id;
        $search = $request->input('search');

        // Get retailer cancellations (orders with status 'cancelled')
        $retailerCancellationsQuery = Order::with(['user', 'orderDetails.product', 'payment'])
            ->where('distributor_id', $distributorId)
            ->where('status', 'cancelled');

        // Get distributor rejections (orders with status 'rejected')
        $myCancellationsQuery = Order::with(['user', 'orderDetails.product', 'payment'])
            ->where('distributor_id', $distributorId)
            ->where('status', 'rejected');

        // Apply search if provided
        if ($search) {
            $this->applySearchToQuery($retailerCancellationsQuery, $search);
            $this->applySearchToQuery($myCancellationsQuery, $search);
        }

        $retailerCancellations = $retailerCancellationsQuery->latest()->get();
        $myCancellations = $myCancellationsQuery->latest()->get();

        // Calculate total amount for each order
        foreach ($retailerCancellations as $order) {
            $order->total_amount = $order->orderDetails->sum('subtotal');
            $order->customer_name = $order->user ? $order->user->first_name . ' ' . $order->user->last_name : 'Unknown';
        }

        foreach ($myCancellations as $order) {
            $order->total_amount = $order->orderDetails->sum('subtotal');
            $order->customer_name = $order->user ? $order->user->first_name . ' ' . $order->user->last_name : 'Unknown';
        }

        return view('distributors.cancellations.index', compact('retailerCancellations', 'myCancellations'));
    }

    /**
     * Apply search filters to query
     */
    private function applySearchToQuery($query, $search)
    {
        $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
                ->orWhereHas('user', function ($subQ) use ($search) {
                    $subQ->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                });
        });
    }

    public function getOrderDetails($orderId)
    {
        $order = Order::with([
            'user',
            'orderDetails.product',
            'payment'
        ])->findOrFail($orderId);

        // Determine the reason based on order status
        $reason = $order->status == 'cancelled' ? $order->cancel_reason : $order->reject_reason;

        return response()->json([
            'order' => $order,
            'formatted_id' => $order->formatted_order_id,
            'customer' => $order->user->first_name . ' ' . $order->user->last_name,
            'items' => $order->orderDetails,
            'total' => $order->orderDetails->sum('subtotal'),
            'reason' => $reason ?? 'No reason provided'
        ]);
    }

    public function delete($id)
    {
        $order = Order::findOrFail($id);
        $distributorId = Auth::user()->distributor->id;

        // Check if the order belongs to the distributor
        if ($order->distributor_id !== $distributorId) {
            return redirect()->back()->with('error', 'Unauthorized action');
        }

        try {
            DB::beginTransaction();
            $order->delete();
            DB::commit();
            
            return redirect()->back()->with('success', 'Order deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    public function batchDelete(Request $request)
    {
        $request->validate([
            'selected_orders' => 'required|array',
            'selected_orders.*' => 'exists:orders,id',
        ]);

        $distributorId = Auth::user()->distributor->id;

        try {
            DB::beginTransaction();
            
            // Get orders that belong to this distributor
            $orders = Order::whereIn('id', $request->selected_orders)
                ->where('distributor_id', $distributorId)
                ->get();

            if ($orders->isEmpty()) {
                return redirect()->back()->with('error', 'No valid orders selected for deletion');
            }

            foreach ($orders as $order) {
                $order->delete();
            }
            
            DB::commit();
            return redirect()->back()->with('success', count($orders) . ' orders deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete orders: ' . $e->getMessage());
        }
    }
}