<?php

namespace App\Http\Controllers\Distributors;

use Illuminate\Http\Request;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;

class ReturnRequestController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $distributorId = Auth::user()->distributor->id;
        $search = $request->search;

        // Get pending return requests
        $pendingReturnsQuery = ReturnRequest::with(['order', 'retailer', 'items.orderDetail.product'])
            ->whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            })
            ->where('status', 'pending')
            ->latest();

        // Get approved/rejected return requests
        $completedReturnsQuery = ReturnRequest::with(['order', 'retailer', 'items.orderDetail.product'])
            ->whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            })
            ->whereIn('status', ['approved', 'rejected'])
            ->latest();

        // Apply search if provided
        if ($search) {
            $pendingReturnsQuery->whereHas('order', function ($query) use ($search) {
                $query->where('order_id', 'like', "%{$search}%");
            })
                ->orWhereHas('retailer', function ($query) use ($search) {
                    $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                });

            $completedReturnsQuery->whereHas('order', function ($query) use ($search) {
                $query->where('order_id', 'like', "%{$search}%");
            })
                ->orWhereHas('retailer', function ($query) use ($search) {
                    $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                });
        }

        $pendingReturns = $pendingReturnsQuery->paginate(10);
        $completedReturns = $completedReturnsQuery->paginate(10);

        // Preserve query string parameters in pagination links
        if ($request->has('search')) {
            $pendingReturns->appends(['search' => $request->search]);
            $completedReturns->appends(['search' => $request->search]);
        }

        return view('distributors.returns.index', compact('pendingReturns', 'completedReturns'));
    }

    public function show($id)
    {
        $distributorId = Auth::user()->distributor->id;

        // Get return request with related data
        $return = ReturnRequest::with([
            'order',
            'retailer',
            'items.orderDetail.product'
        ])
            ->whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            })
            ->findOrFail($id);

        // Transform the items data to include product information
        $return->items->transform(function ($item) {
            $item->product = $item->orderDetail->product;
            return $item;
        });

        return response()->json([
            'success' => true,
            'return' => $return
        ]);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $distributorId = Auth::user()->distributor->id;

        try {
            DB::beginTransaction();

            // Get return request and verify ownership
            $returnRequest = ReturnRequest::whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            })->findOrFail($id);

            // Check if return request is still pending
            if ($returnRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This return request has already been processed.'
                ], 400);
            }

            // Update return request
            $returnRequest->update([
                'status' => 'rejected',
                'reject_reason' => $request->rejection_reason,
                'processed_at' => now()
            ]);

            // Update order status back to completed if it was marked as returned
            if ($returnRequest->order->status === 'returned') {
                $returnRequest->order->update([
                    'status' => 'completed'
                ]);
            }

            // Send notification to retailer
            $this->notificationService->create(
                $returnRequest->retailer_id,
                'return_rejected',
                [
                    'title' => 'Return Request Rejected',
                    'message' => "Your return request for order #{$returnRequest->order->formatted_order_id} has been rejected.",
                    'reason' => $request->rejection_reason,
                    'order_id' => $returnRequest->order_id,
                    'recipient_type' => 'retailer'
                ],
                $returnRequest->order_id
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Return request rejection failed: ' . $e->getMessage());
        }
        return redirect()->back()->with('success', 'Return request has been rejected successfully.');
    }

    public function approve(Request $request, $id)
    {
        $distributorId = Auth::user()->distributor->id;

        try {
            DB::beginTransaction();

            // Get return request and verify ownership
            $returnRequest = ReturnRequest::whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            })
                ->with(['order', 'items.orderDetail.product'])
                ->findOrFail($id);

            // Check if return request is still pending
            if ($returnRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This return request has already been processed.'
                ], 400);
            }

            // Calculate refund amount
            $refundAmount = 0;
            foreach ($returnRequest->items as $item) {
                $refundAmount += $item->orderDetail->price * $item->quantity;

                // Return items to inventory
                $product = $item->orderDetail->product;
                $product->stock_quantity += $item->quantity;
                $product->save();
            }

            // Create refund record
            $refund = DB::table('refunds')->insert([
                'return_request_id' => $returnRequest->id,
                'order_id' => $returnRequest->order_id,
                'amount' => $refundAmount,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update distributor earnings
            $earning = DB::table('earnings')
                ->where('distributor_id', $distributorId)
                ->where('payment_id', $returnRequest->order->payment->id)
                ->first();

            if ($earning) {
                DB::table('earnings')
                    ->where('id', $earning->id)
                    ->update([
                        'amount' => DB::raw("amount - {$refundAmount}"),
                        'updated_at' => now()
                    ]);
            }

            // Update return request status
            $returnRequest->update([
                'status' => 'approved',
                'processed_at' => now()
            ]);

            // Update order status to returned
            $returnRequest->order->update([
                'status' => 'returned',
                'status_updated_at' => now() // Add timestamp for consistency
            ]);

            // Send notification to retailer
            $this->notificationService->create(
                $returnRequest->retailer_id,
                'return_approved',
                [
                    'title' => 'Return Request Approved',
                    'message' => "Your return request for order #{$returnRequest->order->formatted_order_id} has been approved. Refund amount: ₱" . number_format($refundAmount, 2),
                    'order_id' => $returnRequest->order_id,
                    'refund_amount' => $refundAmount,
                    'recipient_type' => 'retailer'
                ],
                $returnRequest->order_id
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Return request approved successfully. Refund process initiated.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Return request approval failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while approving the return request.'
            ], 500);
        }
    }


    public function export()
    {
        $distributorId = Auth::user()->distributor->id;

        // Get all return requests for this distributor
        $returns = ReturnRequest::with(['order', 'retailer', 'items.orderDetail.product'])
            ->whereHas('order', function ($query) use ($distributorId) {
                $query->where('distributor_id', $distributorId);
            })
            ->latest()
            ->get();

        $pdf = PDF::loadView('distributors.returns.pdf', compact('returns'));

        return $pdf->download('returns-report-' . date('Y-m-d') . '.pdf');
    }
}
