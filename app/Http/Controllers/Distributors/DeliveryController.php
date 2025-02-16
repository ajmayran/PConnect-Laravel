<?php

namespace App\Http\Controllers\Distributors;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Delivery;
use App\Models\Trucks;

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
        $deliveries = Delivery::with([
            'order.user.retailerProfile',
            'order.orderDetails' => function ($query) {
                $query->latest()->take(1); // Get only the latest order detail
            }
        ])
            ->whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            })
            ->where('status', self::STATUS_PENDING)
            ->latest()
            ->get();

        $availableTrucks = Trucks::where('distributor_id', Auth::user()->distributor->id)
            ->where('status', 'available')
            ->get();

        return view('distributors.delivery.index', compact('deliveries', 'availableTrucks'));
    }

    public function updateStatus(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_transit,out_for_delivery,delivered,failed'
        ]);

        $delivery->update([
            'status' => $validated['status'],
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Delivery status updated successfully');
    }
}
