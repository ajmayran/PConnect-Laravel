<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Order;
use App\Models\Trucks;
use App\Models\Payment;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

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
        $search = request('search');

        // Build base query to include both regular and exchange deliveries
        $query = Delivery::with([
            'order',
            'order.user.retailerProfile',
            'order.orderDetails.product',
            'returnRequest.items.orderDetail.product' // Add this relationship for exchange deliveries
        ])
            ->whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            });

        // Handle special "exchanges" filter
        if (request('view') === 'exchanges') {
            $query->where(function ($q) {
                $q->where('is_exchange_delivery', true)
                    ->orWhereNotNull('exchange_for_return_id');
            });
        }
        // Handle regular status filtering
        else if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Apply search if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($sq) use ($search) {
                        $sq->where('formatted_order_id', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($usq) use ($search) {
                                $usq->where( DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                            });
                    });
            });
        }

        $deliveries = $query->latest()->paginate(10);
        $deliveries->appends(request()->query());

        // Get available trucks for assigning deliveries
        $availableTrucks = Trucks::where('distributor_id', $distributorId)
            ->where('status', 'available')
            ->get();

        return view('distributors.delivery.index', compact('deliveries', 'availableTrucks', 'status'));
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

    public function markDelivered(Request $request, Delivery $delivery, Order $order)
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
        $delivery->update([
            'status' => 'delivered',
            'updated_at' => now()
        ]);

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

            // Get the notification service
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
