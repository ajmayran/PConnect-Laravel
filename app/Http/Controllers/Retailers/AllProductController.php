<?php

namespace App\Http\Controllers\Retailers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;

class AllProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('distributor')
                       ->where('stock_quantity', '>', 0)
                       ->where('status', 'active'); // Only show active products

        // Get categories for the filter
        $categories = Category::all();
        
        // Filter by category if selected
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(15);

        return view('retailers.all-product', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $request->get('category', 'all')
        ]);
    }
}
