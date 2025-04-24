<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Refund;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class DistributorRefundController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the refunds.
     */
    public function index(Request $request)
    {
        $distributorId = Auth::user()->distributor->id;
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        // Base query
        $query = Refund::with(['returnRequest', 'order', 'order.user'])
            ->whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            });

        // Filter by status if not 'all'
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Apply search if provided
        if ($search) {
            $query->whereHas('order', function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%");
            })
                ->orWhereHas('order.user', function ($q) use ($search) {
                    $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                });
        }

        $refunds = $query->latest()->paginate(10);

        return view('distributors.refunds.index', compact('refunds', 'status'));
    }

    public function processRefund(Request $request, $id)
    {
        $refund = Refund::findOrFail($id);

        // Check if this distributor owns the refund
        $distributorId = Auth::user()->distributor->id;
        if ($refund->order->distributor_id != $distributorId) {
            return redirect()->back()->with('error', 'Unauthorized access to this refund.');
        }

        // Only allow processing refunds that are in 'processing' status
        if ($refund->status !== 'processing') {
            return redirect()->back()->with('error', 'This refund cannot be processed in its current state.');
        }

        $request->validate([
            'scheduled_date' => 'required|date|after_or_equal:today',
        ]);

        try {
            DB::beginTransaction();

            // Update the scheduled delivery date
            $scheduledDate = $request->input('scheduled_date');
            $refund->update([
                'scheduled_date' => $scheduledDate,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'status' => 'pending_delivery'
            ]);

            // Send notification to the retailer
            $this->notificationService->create(
                $refund->order->user_id,
                'refund_scheduled',
                [
                    'title' => 'Refund Scheduled',
                    'message' => "Your refund of ₱" . number_format($refund->amount, 2) . " for order #{$refund->order->formatted_order_id} has been scheduled for delivery on " . \Carbon\Carbon::parse($scheduledDate)->format('M d, Y') . ".",
                    'order_id' => $refund->order_id,
                    'recipient_type' => 'retailer'
                ],
                $refund->order_id
            );

            DB::commit();
            return redirect()->back()->with('success', 'Refund has been scheduled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund processing failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process refund: ' . $e->getMessage());
        }
    }


    public function completeRefund($id)
    {
        $refund = Refund::findOrFail($id);

        // Check if this distributor owns the refund
        $distributorId = Auth::user()->distributor->id;
        if ($refund->order->distributor_id != $distributorId) {
            return redirect()->back()->with('error', 'Unauthorized access to this refund.');
        }

        // Only allow completing refunds that are in 'pending_delivery' status
        if ($refund->status !== 'pending_delivery') {
            return redirect()->back()->with('error', 'This refund cannot be completed in its current state.');
        }

        try {
            DB::beginTransaction();

            // Update the refund status to 'completed'
            $refund->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            // Update the payment status of the associated order to 'failed'
            $payment = $refund->order->payment;
            if ($payment) {
                $payment->update([
                    'payment_status' => 'failed',
                    'payment_note' => 'Refund completed for this order.',
                    'paid_at' => null // Remove the paid timestamp
                ]);
            }

            // Add refunded items back to stock
            foreach ($refund->returnRequest->items as $item) {
                $product = $item->orderDetail->product;

                if ($product->isBatchManaged()) {
                    // Add stock back to the appropriate batches
                    $remainingQuantity = $item->quantity;

                    // Get batches ordered by expiry date
                    $batches = $product->batches()
                        ->where('quantity', '>', 0)
                        ->orderBy('expiry_date')
                        ->get();

                    foreach ($batches as $batch) {
                        if ($remainingQuantity <= 0) break;

                        $quantityToAdd = min($batch->quantity, $remainingQuantity);

                        // Update batch quantity
                        $batch->quantity += $quantityToAdd;
                        $batch->save();

                        // Create stock-in record
                        Stock::create([
                            'product_id' => $product->id,
                            'batch_id' => $batch->id,
                            'type' => 'in',
                            'quantity' => $quantityToAdd,
                            'user_id' => Auth::id(),
                            'notes' => "Refund completed for Return Request #{$refund->returnRequest->id}",
                            'stock_updated_at' => now(),
                        ]);

                        $remainingQuantity -= $quantityToAdd;
                    }

                    // If remaining quantity exists, create a new batch
                    if ($remainingQuantity > 0) {
                        $newBatch = $product->batches()->create([
                            'batch_number' => 'new-' . uniqid(),
                            'quantity' => $remainingQuantity,
                            'expiry_date' => now()->addMonths(6), // Default expiry date
                            'received_at' => now(),
                        ]);

                        Stock::create([
                            'product_id' => $product->id,
                            'batch_id' => $newBatch->id,
                            'type' => 'in',
                            'quantity' => $remainingQuantity,
                            'user_id' => Auth::id(),
                            'notes' => "Refund completed for Return Request #{$refund->returnRequest->id}",
                            'stock_updated_at' => now(),
                        ]);
                    }
                } else {
                    // Handle non-batch products
                    Stock::create([
                        'product_id' => $product->id,
                        'batch_id' => null,
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'user_id' => Auth::id(),
                        'notes' => "Refund completed for Return Request #{$refund->returnRequest->id}",
                        'stock_updated_at' => now(),
                    ]);

                    // Update product stock quantity
                    $product->stock_quantity += $item->quantity;
                    $product->save();
                }
            }

            // Send notification to the retailer
            $this->notificationService->create(
                $refund->order->user_id,
                'refund_completed',
                [
                    'title' => 'Refund Completed',
                    'message' => "Your refund of ₱" . number_format($refund->amount, 2) . " for order #{$refund->order->formatted_order_id} has been completed.",
                    'order_id' => $refund->order_id,
                    'recipient_type' => 'retailer'
                ],
                $refund->order_id
            );

            DB::commit();
            return redirect()->back()->with('success', 'Refund has been marked as completed, payment status updated to failed, and stock has been updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund completion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to complete refund: ' . $e->getMessage());
        }
    }
}
