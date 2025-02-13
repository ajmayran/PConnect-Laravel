<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class Distributor extends Controller
{
    public function pendingDistributors()
    {
        $pendingDistributors = User::where('user_type', 'distributor')
            ->where('status', 'pending')
            ->get();
        return view('admin.distributors.pending', compact('pendingDistributors'));
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
    public function approvedDistributors()
{
    $approvedDistributors = User::where('user_type', 'distributor')
        ->where('status', 'approved')
        ->get();
    return view('admin.distributors.approved', compact('approvedDistributors'));
}

public function distributorProducts($id)
{
    $distributor = User::with('products')->findOrFail($id);
    return view('admin.distributors.products', compact('distributor'));
}

public function removeProduct(Request $request, $id)
{
    $product = Product::findOrFail($id);
    $product->delete();

    return redirect()->back()->with('success', 'Product removed successfully. Reason: ' . $request->input('reason'));
}
}
