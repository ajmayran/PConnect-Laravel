<?php

namespace App\Http\Controllers\Retailers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Distributors;
use Illuminate\Http\Request;
use App\Models\BlockedRetailer;
use App\Models\DistributorReport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class DistributorPageController extends Controller
{
    public function show($id, Request $request)
    {
        $retailerId = Auth::id();
        $distributor = Distributors::findOrFail($id);

        // Check if this distributor has blocked the retailer
        $isBlocked = BlockedRetailer::where('distributor_id', $distributor->user_id)
            ->where('retailer_id', $retailerId)
            ->exists();

        // Get categories for the distributor
        $categories = Category::whereHas('products', function ($query) use ($id) {
            $query->where('distributor_id', $id);
        })->get();

        // Selected category
        $selectedCategory = $request->category ?? 'all';

        // Only get products if not blocked
        if (!$isBlocked) {
            $productsQuery = Product::where('distributor_id', $id)
                ->where('stock_quantity', '>', 0);

            if ($selectedCategory !== 'all') {
                $productsQuery->where('category_id', $selectedCategory);
            }

            // Apply pagination - 10 products per page
            $products = $productsQuery->paginate(10);
        } else {
            // Create an empty paginator if blocked
            $products = Product::where('id', 0)->paginate(10);
        }

        return view('retailers.distributor-page', [
            'distributor' => $distributor,
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'isBlocked' => $isBlocked
        ]);
    }

    public function reportDistributor(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
            'details' => 'nullable|string',
        ]);

        $user = Auth::user();
        $distributor = Distributors::findOrFail($id);

        // Create report record
        DistributorReport::create([
            'retailer_id' => $user->id,
            'distributor_id' => $id,
            'reason' => $request->reason,
            'details' => $request->details,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Report submitted successfully. Our team will review it shortly.');
    }
}