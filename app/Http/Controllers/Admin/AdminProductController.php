<?php 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class AdminProductController extends Controller
{
    public function approveProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'accepted']); // Ensure the value is properly quoted

        return redirect()->back()->with('success', 'Product approved successfully');
    }

    public function pendingProducts()
{
    $pendingProducts = Product::where('status', 'pending')->get();
    return view('admin.products.pending', compact('pendingProducts'));
}

    public function rejectProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'rejected', 'rejection_reason' => $request->input('reason')]); // Ensure the value is properly quoted

        return redirect()->back()->with('success', 'Product rejected successfully. Reason: ' . $request->input('reason'));
    }
}