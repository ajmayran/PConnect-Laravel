<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Distributors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', ['user' => Auth::user()]);
    }

    public function pendingDistributors()
    {
        $pendingDistributors = User::where('user_type', 'distributor')
            ->where('status', 'pending')
            ->get();
        return view('admin.distributors.index', compact('pendingDistributors'));
    }
    public function acceptDistributor($id)
    {
        $distributor = User::findOrFail($id);
        $distributor->update([
            'status' => 'approved'
        ]);

        return redirect()->back()->with('success', 'Distributor approved successfully');
    }

    public function declineDistributor($id)
    {
        $distributor = User::findOrFail($id);
        $distributor->update([
            'status' => 'rejected'
        ]);

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
}
