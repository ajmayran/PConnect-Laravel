<?php

namespace App\Http\Controllers\Retailers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Distributors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class DistributorPageController extends Controller
{
    public function show($id, Request $request)
    {
        // Get distributor directly from Distributors model
        $distributor = Distributors::where('id', $id)
            ->firstOrFail();

        if ($distributor->barangay) {
            $barangay = DB::table('barangays')->where('code', $distributor->barangay)->first();
            if ($barangay) {
                $distributor->barangay_name = $barangay->name;
            } else {
                $distributor->barangay_name = $distributor->barangay; // Use code as fallback
            }
        }

        $selectedCategory = $request->get('category', 'all');
        $categories = Category::all();

        // Use distributor's user_id for product query
        $productsQuery = Product::where('distributor_id', $distributor->id)
            ->where('status', 'pending')
            ->where('stock_quantity', '>', 0);

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
