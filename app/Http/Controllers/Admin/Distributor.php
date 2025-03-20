<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Product;
use App\Models\Distributors;
use Illuminate\Http\Request;
use App\Events\DistributorApproved;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\DistributorApprovalMail;
use Illuminate\Support\Facades\Storage;

class Distributor extends Controller
{

    public function pendingDistributors()
    {
        $pendingDistributors = User::where('user_type', 'distributor')
            ->where('status', 'pending')
            ->with('credentials') // Eager-load the credentials relationship
            ->get();

        foreach ($pendingDistributors as $distributor) {
            logger('Distributor ID: ' . $distributor->id);
            logger('Credentials: ' . $distributor->credentials);
        }

        return view('admin.distributors.pending', compact('pendingDistributors'));
    }

    public function acceptDistributor($id)
    {
        $distributor = User::findOrFail($id);
        $distributor->update([
            'status' => 'approved'
        ]);

        // Fire event
        event(new DistributorApproved($distributor));

        // Send acceptance email to distributor
        try {
            Mail::to($distributor->email)->send(new DistributorApprovalMail($distributor));
            return redirect()->back()->with('success', 'Distributor approved successfully and notification email sent.');
        } catch (\Exception $e) {
            Log::error('Failed to send distributor approval email: ' . $e->getMessage());
            return redirect()->back()->with('success', 'Distributor approved successfully but notification email could not be sent.');
        }
    }

    public function declineDistributor(Request $request, $id)
    {
        $distributor = User::findOrFail($id);
    
        // Update the user's status to rejected and save the rejection reason
        $distributor->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('reason'), // Save rejection reason in the users table
        ]);
    
        logger('Rejection reason saved: ' . $request->input('reason')); // Debug log
    
        return redirect()->back()->with('success', 'Distributor application declined');
    }
    public function downloadCredential($id)
    {
        try {
            $distributor = User::with('credential')->findOrFail($id);

            if (!$distributor->credential) {
                return back()->with('error', 'No credential file found for this distributor.');
            }

            $filePath = storage_path('app/public/credentials/' . $distributor->credential->file_path);

            if (!Storage::disk('public')->exists('credentials/' . $distributor->credential->file_path)) {
                return back()->with('error', 'Credential file is missing from storage.');
            }

            return response()->download($filePath);
        } catch (\Exception $e) {
            return back()->with('error', 'Error downloading file: ' . $e->getMessage());
        }
    }

    public function approvedDistributors()
    {
        $approvedDistributors = User::where('user_type', 'distributor')
            ->where('status', 'approved')
            ->with('distributor')
            ->get();
        return view('admin.distributors.approved', compact('approvedDistributors'));
    }

    public function allDistributors()
    {
        $distributors = Distributors::with('user')->get();
        return view('admin.distributors.all', compact('distributors'));
    }


    public function rejectedDistributors()
{
    // Fetch all rejected distributors
    $rejectedDistributors = User::where('user_type', 'distributor')
        ->where('status', 'rejected')
        ->paginate(10);

    ($rejectedDistributors->items());

    return view('admin.distributors.rejected', compact('rejectedDistributors'));
}

    public function viewInformation($id)
{
    $distributor = User::with('credentials')->findOrFail($id);

    return view('admin.distributors.view-information', compact('distributor'));
}

}
}