<?php

namespace App\Http\Controllers\Distributors;

use App\Http\Controllers\Controller;
use App\Models\BlockedRetailer;
use App\Models\RetailerReport;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetailerActionsController extends Controller
{
    protected $notificationService;
    
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    
    public function reportRetailer(Request $request, User $retailer)
    {
        $request->validate([
            'reason' => 'required|string',
            'details' => 'nullable|string|max:1000',
        ]);

        $report = RetailerReport::create([
            'distributor_id' => Auth::id(),
            'retailer_id' => $retailer->id,
            'reason' => $request->reason,
            'details' => $request->details,
            'status' => 'pending'
        ]);

        // Create notification for admin
        $this->notificationService->create(
            1, // Assuming admin ID is 1, adjust as needed
            'retailer_reported',
            [
                'title' => 'Retailer Report Submitted',
                'message' => "A distributor has reported retailer {$retailer->name}.",
                'report_id' => $report->id
            ]
        );

        return redirect()->back()->with('success', 'Report submitted successfully. Our team will review it shortly.');
    }

    public function toggleBlockRetailer(User $retailer)
    {
        $distributorId = Auth::id();
        
        $existingBlock = BlockedRetailer::where('distributor_id', $distributorId)
            ->where('retailer_id', $retailer->id)
            ->first();
        
        if ($existingBlock) {
            // Unblock
            $existingBlock->delete();
            $message = "Retailer {$retailer->first_name} {$retailer->last_name} has been unblocked.";
        } else {
            // Block
            BlockedRetailer::create([
                'distributor_id' => $distributorId,
                'retailer_id' => $retailer->id,
                'reason' => 'Blocked by distributor'
            ]);
            $message = "Retailer {$retailer->first_name} {$retailer->last_name} has been blocked.";
        }
        
        return redirect()->back()->with('success', $message);
    }
}