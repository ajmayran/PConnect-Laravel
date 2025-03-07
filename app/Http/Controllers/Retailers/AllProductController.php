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
        $selectedCategory = $request->get('category', 'all');
        $query = Product::query();
        if ($selectedCategory !== 'all') {
            $query->where('category_id', $selectedCategory);
        }
        $query->where('stock_quantity', '>', 0);
        $products = $query->paginate(15);
        $categories = Category::all();
        return view('retailers.all-product', compact('products', 'categories', 'selectedCategory'));
    }
}
