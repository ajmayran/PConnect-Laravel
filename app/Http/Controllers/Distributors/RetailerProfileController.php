<?php

namespace App\Http\Controllers\Distributors;

use App\Models\User;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RetailerProfileController extends Controller
{

    public function show($id)
    {
        // Get the distributor ID
        $distributorId = Auth::user()->distributor->id;

        // Find the retailer
        $retailer = User::with('retailerProfile')
            ->where('id', $id)
            ->where('user_type', 'retailer')
            ->firstOrFail();

        $recentOrders = Order::where('user_id', $retailer->id)
            ->where('status', 'completed')
            ->with(['orderDetails.product', 'payment', 'user.retailerProfile', 'distributor'])
            ->latest()
            ->take(5)
            ->get();

        // Get order statistics - keep these as they are for the dashboard cards
        $orderStats = [
            'total' => Order::where('user_id', $retailer->id)
                ->where('distributor_id', $distributorId)
                ->count(),
            'completed' => Order::where('user_id', $retailer->id)
                ->where('distributor_id', $distributorId)
                ->where('status', 'completed')
                ->count(),
            'processing' => Order::where('user_id', $retailer->id)
                ->where('distributor_id', $distributorId)
                ->where('status', 'processing')
                ->count(),
            'totalSpent' => Order::where('user_id', $retailer->id)
                ->where('distributor_id', $distributorId)
                ->whereHas('payment', function ($query) {
                    $query->where('payment_status', 'paid');
                })
                ->sum('total_amount')
        ];

        return view('distributors.retailers.show', compact('retailer', 'recentOrders', 'orderStats'));
    }

    public function getRetailerOrders($retailerId)
    {
        $distributorId = Auth::id();

        $orders = Order::where('user_id', $retailerId)
            ->where('status', 'completed')
            ->with(['orderDetails.product', 'payment', 'user.retailerProfile', 'distributor'])
            ->latest()
            ->get();

        return response()->json([
            'orders' => $orders
        ]);
    }
}
