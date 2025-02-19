<?php 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Distributors;
use Illuminate\Support\Facades\Mail;

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

    public function allProducts()
    {
        $products = Product::with('distributor.user')
        ->where('status', 'accepted')
        ->get();
        
        return view('admin.products.all', compact('products'));
    }

    public function distributorProducts($id)
    {
        $distributor = Distributors::findOrFail($id);
        $products = Product::where('distributor_id', $id)
            ->with(['distributor' => function ($query) {
                $query->with('user'); // Eager load the user relationship for the distributor
            }])
            ->get();
        
        return view('admin.products.distributor', compact('products', 'distributor'));
    }

    public function removeProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $distributor = $product->distributor;

        // Send email to distributor
        Mail::send('emails.product_removed', ['product' => $product, 'reason' => $request->input('reason')], function ($message) use ($distributor) {
            $message->to($distributor->company_email)
                ->subject('Product Removed');
        });

        $product->delete();

        return redirect()->back()->with('success', 'Product removed successfully. Reason: ' . $request->input('reason'));
    }
}