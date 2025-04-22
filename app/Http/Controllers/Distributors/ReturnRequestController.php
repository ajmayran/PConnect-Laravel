<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Stock;
use Illuminate\Http\Request;
use App\Models\ReturnRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ReturnRequestItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

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
    
            // Calculate refund amount and adjust stock
            $refundAmount = 0;
            foreach ($returnRequest->items as $item) {
                $refundAmount += $item->orderDetail->price * $item->quantity;
    
                $product = $item->orderDetail->product;
    
                if ($product->isBatchManaged()) {
                    // First check for soft-deleted batches related to this order
                    $deletedBatches = \App\Models\ProductBatch::withTrashed()
                        ->where('product_id', $product->id)
                        ->whereNotNull('deleted_at')
                        ->orderBy('expiry_date')
                        ->get();
                        
                    $remainingQuantity = $item->quantity;
                    $restoredDeletedBatch = false;
                    
                    // Try to restore deleted batches first
                    foreach ($deletedBatches as $deletedBatch) {
                        if ($remainingQuantity <= 0) break;
                        
                        Log::info('Examining deleted batch for return restoration', [
                            'batch_id' => $deletedBatch->id,
                            'batch_number' => $deletedBatch->batch_number,
                            'deleted_at' => $deletedBatch->deleted_at
                        ]);
                        
                        // Find stock record related to this batch for this order
                        $originalBatchQuantity = \App\Models\Stock::where('batch_id', $deletedBatch->id)
                            ->where('type', 'out')
                            ->where('notes', 'like', '%Order ' . $returnRequest->order->formatted_order_id . '%')
                            ->sum('quantity');
                        
                        // If we can't find the original quantity, make a reasonable estimate
                        if ($originalBatchQuantity <= 0) {
                            $originalBatchQuantity = min($remainingQuantity, 25); // Use a reasonable default
                            Log::info('Could not determine original batch quantity for return, using estimate', [
                                'estimated_quantity' => $originalBatchQuantity
                            ]);
                        } else {
                            Log::info('Found original batch quantity from stock records for return', [
                                'original_quantity' => $originalBatchQuantity
                            ]);
                        }
                        
                        // Restore the batch
                        $deletedBatch->restore();
                        
                        // Use either the original quantity or remaining quantity, whichever is smaller
                        $quantityToRestore = min($originalBatchQuantity, $remainingQuantity);
                        $deletedBatch->quantity = $quantityToRestore;
                        $deletedBatch->save();
                        
                        // Create stock record for the restoration
                        Stock::create([
                            'product_id' => $product->id,
                            'batch_id' => $deletedBatch->id,
                            'type' => 'in',
                            'quantity' => $quantityToRestore,
                            'user_id' => Auth::id(),
                            'notes' => "Return Request #{$returnRequest->id} for Order #{$returnRequest->order->formatted_order_id} approved - restored deleted batch #" . $deletedBatch->batch_number,
                            'stock_updated_at' => now()
                        ]);
                        
                        Log::info('Restored deleted batch for return request', [
                            'batch_id' => $deletedBatch->id,
                            'batch_number' => $deletedBatch->batch_number,
                            'quantity' => $quantityToRestore
                        ]);
                        
                        $remainingQuantity -= $quantityToRestore;
                        $restoredDeletedBatch = true;
                    }
                    
                    // If there's still remaining quantity after restoring deleted batches
                    if ($remainingQuantity > 0) {
                        // Handle batch-managed products using existing batches
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
                                'notes' => "Return Request #{$returnRequest->id} for Order #{$returnRequest->order->formatted_order_id} approved",
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
                                'notes' => "Return Request #{$returnRequest->id} for Order #{$returnRequest->order->formatted_order_id} approved",
                                'stock_updated_at' => now(),
                            ]);
                        }
                    }
                } else {
                    // Handle non-batch products
                    Stock::create([
                        'product_id' => $product->id,
                        'batch_id' => null,
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'user_id' => Auth::id(),
                        'notes' => "Return Request #{$returnRequest->id} for Order #{$returnRequest->order->formatted_order_id} approved",
                        'stock_updated_at' => now(),
                    ]);
                }
    
                // Update product's stock_updated_at timestamp
                $product->update(['stock_updated_at' => now()]);
            }
    
            // Create refund record
            DB::table('refunds')->insert([
                'return_request_id' => $returnRequest->id,
                'order_id' => $returnRequest->order_id,
                'amount' => $refundAmount,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            // Update return request status
            $returnRequest->update([
                'status' => 'approved',
                'processed_at' => now()
            ]);
    
            // Update order status to returned
            $returnRequest->order->update([
                'status' => 'returned',
                'status_updated_at' => now()
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
