<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Earning;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['order.user', 'order.orderDetails'])->latest();

        if ($request->status && $request->status !== 'all') {
            $query->where('payment_status', $request->status);
        }

        $payments = $query->get();
        return view('distributors.payments.index', compact('payments'));
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        try {
            $request->validate([
                'payment_status' => 'required|in:paid,failed',
                'payment_note' => 'nullable|string|max:255'
            ]);

            DB::beginTransaction();

            $payment->update([
                'payment_status' => $request->payment_status,
                'payment_note' => $request->payment_note,
                'paid_at' => $request->payment_status === 'paid' ? now() : null
            ]);

            if ($request->payment_status === 'paid') {
                $totalAmount = $payment->order->orderDetails->sum('subtotal');

                Earning::create([
                    'payment_id' => $payment->id,
                    'distributor_id' => $payment->distributor_id,
                    'amount' => $totalAmount
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment status'
            ], 500);
        }
    }
}
