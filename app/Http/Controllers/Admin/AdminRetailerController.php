<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Credential;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class AdminRetailerController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // Other existing methods...

    public function retailerCredentials()
    {
        $retailers = User::where('user_type', 'retailer')
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere('status', 'rejected');
            })
            ->with('credentials')
            ->paginate(10);

        return view('admin.retailers.credentials', compact('retailers'));
    }

    public function approveCredentials($id)
    {
        $retailer = User::findOrFail($id);

        if ($retailer->user_type !== 'retailer') {
            return redirect()->back()->with('error', 'This user is not a retailer.');
        }

        $retailer->update(['status' => 'approved']);

        // Notify the retailer
        $this->notificationService->create(
            $retailer->id,
            'credentials_approved',
            [
                'title' => 'Credentials Approved',
                'message' => 'Your business credentials have been verified and approved. You now have full access to the platform.',
                'recipient_type' => 'retailer'
            ]
        );

        return redirect()->back()->with('success', 'Retailer credentials approved successfully.');
    }

    public function rejectCredentials(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $retailer = User::findOrFail($id);

        if ($retailer->user_type !== 'retailer') {
            return redirect()->back()->with('error', 'This user is not a retailer.');
        }

        $retailer->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        return redirect()->back()->with('success', 'Retailer credentials rejected successfully.');
    }

    public function rejectCredential(Request $request, $id)
    {
        $retailer = User::findOrFail($id);

        if ($retailer->user_type !== 'retailer') {
            return redirect()->back()->with('error', 'This user is not a retailer.');
        }

        $retailer->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);


        return redirect()->back()->with('success', 'Retailer credentials rejected successfully.');
    }
}
