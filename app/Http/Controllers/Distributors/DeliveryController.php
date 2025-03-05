<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Trucks;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
        $deliveries = Delivery::with([
            'order',
            'order.user.retailerProfile',
            'order.orderDetails.product'
        ])
            ->whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            })
            ->where('status', $status)
            ->oldest()
            ->paginate(10); 

        $availableTrucks = Trucks::where('distributor_id', Auth::user()->distributor->id)
            ->where('status', 'available')
            ->get();

        return view('distributors.delivery.index', compact('deliveries', 'availableTrucks'));
    }

    public function updateStatus(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'status' => ['required', Rule::in(['in_transit', 'out_for_delivery', 'delivered'])],
        ]);

        // Retrieve delivery or fail
        $delivery = Delivery::findOrFail($id);
        $delivery->status = $validated['status'];
        $delivery->save();

        return redirect()->back()->with('success', 'Delivery status updated successfully.');
    }
}
