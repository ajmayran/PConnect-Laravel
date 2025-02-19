<?php

namespace App\Http\Controllers\Distributors;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class InsightsController extends Controller
{
    public function index()
    {
        $distributor = Auth::user()->distributor;  
        $products = Product::where('distributor_id', $distributor->id)
            ->with('category')  
            ->orderBy('created_at', 'desc') 
            ->get();
        $categories = Category::all();

        // Get product count per category
        $categoryData = [];
        $categoryLabels = [];
        $categoryColors = [
            '#10B981', 
            '#3B82F6', 
            '#F59E0B', 
            '#EF4444', 
            '#8B5CF6', 
            '#EC4899', 
        ];

        foreach ($categories as $index => $category) {
            $count = $products->where('category_id', $category->id)->count();
            if ($count > 0) {
                $categoryLabels[] = $category->name;
                $categoryData[] = $count;
            }
        }

        return view('distributors.insights', compact(
            'products',
            'categories',
            'categoryData',
            'categoryLabels',
            'categoryColors'
        ));
    }
}
