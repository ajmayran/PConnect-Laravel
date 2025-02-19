<?php

namespace App\Http\Controllers\Retailers;

use App\Http\Controllers\Controller;
use App\Models\Distributors;
use App\Models\Product;
use Illuminate\Http\Request;

class RetailerSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Search distributors with approved users
        $distributors = Distributors::whereHas('user', function($q) {
            $q->where('status', 'approved')
              ->where('user_type', 'distributor');
        })
        ->where('company_name', 'like', "%{$query}%")
        ->get();

        // Search products from approved distributors
        $products = Product::where(function($q) use ($query) {
            $q->where('product_name', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('tags', 'like', "%{$query}%");
        })
        ->whereHas('distributor.user', function ($q) {
            $q->where('status', 'approved')
              ->where('user_type', 'distributor');
        })
        ->with(['distributor'])
        ->get();

        return view('retailers.search-results', compact('distributors', 'products'));
    }
}