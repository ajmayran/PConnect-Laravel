<?php

namespace App\Http\Controllers\Retailers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;
use App\Models\BlockedRetailer;

class AllProductController extends Controller
{
    public function index(Request $request)
    {
        $retailerId = Auth::id();

        // Get IDs of distributors who have blocked this retailer
        $blockingDistributorIds = BlockedRetailer::where('retailer_id', $retailerId)
            ->pluck('distributor_id')
            ->toArray();

        $selectedCategory = $request->get('category', 'all');
        $query = Product::query();

        // Filter by category if selected
        if ($selectedCategory !== 'all') {
            $query->where('category_id', $selectedCategory);
        }

        $query->where('price', '>', 0);

        // Exclude products from distributors who blocked this retailer
        $query->whereDoesntHave('distributor', function ($query) use ($blockingDistributorIds) {
            $query->whereIn('user_id', $blockingDistributorIds);
        });

        $query->with(['distributor:id,company_name,user_id', 'discounts' => function ($query) {
            $query->where('is_active', true)
                  ->where('start_date', '<=', now())
                  ->where('end_date', '>=', now());
        }]);

        // Get products with eager loading
        $perPage = ($selectedCategory === 'all') ? 10 : 10;
        $products = $query->with('distributor:id,company_name,user_id')->paginate($perPage);
        $categories = Category::all();

        $hasBlockedDistributors = count($blockingDistributorIds) > 0;

        return view('retailers.all-product', compact(
            'products',
            'categories',
            'selectedCategory',
            'hasBlockedDistributors'
        ));
    }
}
