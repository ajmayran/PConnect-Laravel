<?php

namespace App\Http\Controllers\Distributors;

use Carbon\Carbon;
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

    public function history(Request $request)
    {
        // Parse date filters or use defaults (last 30 days)
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subDays(30);
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        // Get paginated payment history
        $payments = Payment::with(['order.user', 'order.orderDetails'])
            ->whereBetween(DB::raw('COALESCE(paid_at, updated_at, created_at)'), [$dateFrom, $dateTo])
            ->latest('paid_at')
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        // Get payment statistics
        $stats = [
            'total' => Payment::count(),
            'paid' => Payment::where('payment_status', 'paid')->count(),
            'unpaid' => Payment::where('payment_status', 'unpaid')->count(),
            'failed' => Payment::where('payment_status', 'failed')->count(),
        ];

        // Calculate payment totals
        $totalPaid = Payment::where('payment_status', 'paid')
            ->with('order.orderDetails')
            ->get()
            ->sum(function ($payment) {
                return $payment->order->orderDetails->sum('subtotal');
            });

        $totalPending = Payment::where('payment_status', 'unpaid')
            ->with('order.orderDetails')
            ->get()
            ->sum(function ($payment) {
                return $payment->order->orderDetails->sum('subtotal');
            });

        return view('distributors.payments.history', compact('payments', 'stats', 'totalPaid', 'totalPending'));
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        try {
            $request->validate([
                'payment_status' => 'required|in:paid,failed',
                'payment_note' => 'nullable|string|max:255'
            ]);

            DB::transaction(function () use ($request, $payment) {
                // Update payment status
                $payment->update([
                    'payment_status' => $request->payment_status,
                    'payment_note' => $request->payment_note,
                    'paid_at' => $request->payment_status === 'paid' ? now() : null
                ]);

                if ($request->payment_status === 'paid') {
                    // Create earning record
                    $totalAmount = $payment->order->orderDetails->sum('subtotal');
                    Earning::create([
                        'payment_id' => $payment->id,
                        'distributor_id' => $payment->distributor_id,
                        'amount' => $totalAmount
                    ]);

                    // Update order status to completed
                    $payment->order->update([
                        'status' => 'completed',
                        'status_updated_at' => now()
                    ]);
                }
            });

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

    public function batchDelete(Request $request)
    {
        try {
            $request->validate([
                'selected_payments' => 'required|array',
                'selected_payments.*' => 'exists:payments,id'
            ]);

            DB::transaction(function () use ($request) {
                // Get payments that are linked to orders
                $payments = Payment::whereIn('id', $request->selected_payments)->get();

                foreach ($payments as $payment) {
                    // Delete any related earnings
                    Earning::where('payment_id', $payment->id)->delete();

                    // Delete the payment
                    $payment->delete();
                }
            });

            return redirect()->back()->with('success', count($request->selected_payments) . ' payment(s) deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Batch delete failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to delete payments. ' . $e->getMessage());
        }
    }
}
