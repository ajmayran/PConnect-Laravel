<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Review;
use App\Models\Product;
use App\Models\Distributors;
use Illuminate\Http\Request;
use App\Models\DistributorFollower;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductDescController extends Controller
{
    public function show($id)
    {
        $product = Product::findOrFail($id);
        $distributor = Distributors::findOrFail($product->distributor_id);

        // Calculate average rating for the distributor
        $rating = Review::where('distributor_id', $distributor->id)->avg('rating');

        $isFollowing = false;
        if (Auth::check()) {
            $isFollowing = DistributorFollower::where('distributor_id', $product->distributor->id)
                ->where('retailer_id', Auth::id())
                ->exists();
        }

        // Count total products by this distributor
        $productsCount = Product::where('distributor_id', $distributor->id)
            ->where('status', 'accepted')
            ->count();

        $relatedProducts = Product::where('distributor_id', $product->distributor_id)
            ->where('id', '!=', $product->id)
            // ->where('status', 'accepted')
            ->limit(5)
            ->get();

        return view('retailers.products.show', compact(
            'product',
            'distributor',
            'relatedProducts',
            'rating',
            'isFollowing',
            'productsCount'
        ));
    }
}
