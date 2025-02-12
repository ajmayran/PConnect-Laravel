<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Distributors; 
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;


class DistributorPageController extends Controller
{
    public function show($id, Request $request)
    {
        // Get distributor directly from Distributors model
        $distributor = Distributors::where('id', $id)
            ->firstOrFail();

        $selectedCategory = $request->get('category', 'all');
        $categories = Category::all();

        // Use distributor's user_id for product query
        $productsQuery = Product::where('distributor_id', $distributor->id)
            ->where('status', 'pending')
            ->where('stock_quantity' ,'>', 0);

        if ($selectedCategory !== 'all') {
            $productsQuery->where('category_id', $selectedCategory);
        }

        $products = $productsQuery->paginate(12);

        return view('retailers.distributor-page', compact(
            'distributor',
            'products',
            'categories',
            'selectedCategory'
        ));
    }
}
