<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Trucks;
use App\Models\Payment;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

    public function markDelivered(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'payment_status' => ['required', 'string', Rule::in(['paid', 'unpaid'])],
            'payment_note' => ['nullable', 'string', 'max:500'],
        ]);
        // Check if the delivery is in a valid state to be marked as delivered
        if ($delivery->status !== 'out_for_delivery') {
            return back()->with('error', 'This delivery cannot be marked as delivered in its current state.');
        }

        // Update delivery status to delivered
        $delivery->update(['status' => 'delivered']);

        // Find existing payment record and update it, or create a new one if none exists
        $payment = Payment::firstOrNew(
            ['order_id' => $delivery->order_id],
            ['distributor_id' => Auth::user()->distributor->id]
        );

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


        // Update the order status to completed
        if ($delivery->order) {
            $orderStatus = $validated['payment_status'] === 'paid' ? 'completed' : 'delivered';
            $delivery->order->update([
                'status' => $orderStatus,
                'status_updated_at' => now()
            ]);

            Log::info('Order updated:', [
                'order_id' => $delivery->order->id,
                'new_status' => $orderStatus,
                'payment_status' => $validated['payment_status']
            ]);
        }

        // Check if this is the last active delivery for this truck
        $truck = $delivery->trucks()->first();
        if ($truck) {
            // Check if there are any remaining active deliveries for this truck
            $activeDeliveriesCount = $truck->deliveries()
                ->whereIn('status', ['pending', 'in_transit', 'out_for_delivery'])
                ->count();

            // If no more active deliveries are assigned to this truck, mark it as available
            if ($activeDeliveriesCount === 0) {
                $truck->update([
                    'status' => 'available'
                ]);

                // Detach all completed deliveries from the truck
                // $completedDeliveries = $truck->deliveries()
                //     ->where('deliveries.status', 'delivered')  // Specify the table name here
                //     ->pluck('deliveries.id')  // Specify the table name here
                //     ->toArray();

                // $truck->deliveries()->detach($completedDeliveries);
            }
        }

        return back()->with('success', 'Order completed!.');
    }
}
