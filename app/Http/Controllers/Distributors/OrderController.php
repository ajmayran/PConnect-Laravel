<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    public function index()
    {
        $distributorId = Auth::user()->distributor->id;
        $orders = Order::with(['orderDetails.product', 'user.retailerProfile']) // 'user' is the retailer who ordered
            ->where('distributor_id', $distributorId)
            ->latest()
            ->get();

        return view('distributors.orders.index', compact('orders'));
    }
}
