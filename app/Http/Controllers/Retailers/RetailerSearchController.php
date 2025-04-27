<?php

namespace App\Http\Controllers\Retailers;

use App\Http\Controllers\Controller;
use App\Models\Distributors;
use App\Models\Product;
use App\Models\BlockedRetailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetailerSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $retailerId = Auth::id();
        
        // Get IDs of distributors who have blocked this retailer
        $blockingDistributorIds = BlockedRetailer::where('retailer_id', $retailerId)
            ->pluck('distributor_id')
            ->toArray();

        // Search distributors with approved users who haven't blocked the retailer
        $distributors = Distributors::whereHas('user', function($q) {
            $q->where('status', 'approved')
              ->where('user_type', 'distributor');
        })
        ->where('company_name', 'like', "%{$query}%")
        ->whereNotIn('user_id', $blockingDistributorIds)  // Exclude blocking distributors
        ->get();

        // Search products from approved distributors who haven't blocked the retailer
        $products = Product::where(function($q) use ($query) {
            $q->where('product_name', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%")
              ->orWhere('tags', 'like', "%{$query}%");
        })
        ->whereHas('distributor.user', function ($q) {
            $q->where('status', 'approved')
              ->where('user_type', 'distributor');
        })
        ->whereHas('distributor', function($q) use ($blockingDistributorIds) {
            $q->whereNotIn('user_id', $blockingDistributorIds);  // Exclude products from blocking distributors
        })
        ->with(['distributor'])
        ->get();

        // Pass blocked count to view to show potential notification
        $blockedDistributorsCount = count($blockingDistributorIds);

        return view('retailers.search-results', compact('distributors', 'products', 'blockedDistributorsCount'));
    }
}