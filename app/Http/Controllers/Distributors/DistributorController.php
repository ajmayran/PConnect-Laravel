<?php

namespace App\Http\Controllers\Distributors;

use App\Models\Product;
use App\Models\Category;
use App\Models\Distributors;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class DistributorController extends Controller
{

    public function create()
    {
        $categories = Category::all(); // Fetch categories
        return view('distributors.create', compact('categories')); // Pass categories to the view
    }

    public function store(Request $request)
    {
        // Validate and create the distributor account
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255|unique:distributors',
            'company_address' => 'required|string|max:255',
            'company_phone_number' => 'required|string|max:15',
            'company_profile_image' => 'nullable|image|max:2048',
        ]);

        $distributor = Distributors::create([
            'user_id' => Auth::id(),
            'company_name' => $request->company_name,
            'company_email' => $request->company_email,
            'company_address' => $request->company_address,
            'company_phone_number' => $request->company_phone_number,
            // Handle file upload for company_profile_image if provided
        ]);

        // Redirect or return response
        return redirect()->route('distributors.dashboard')->with('status', 'Distributor account created.');
    }

    public function approvalWaiting()
    {
        return view('auth.approval-waiting');
    }

    public function show($id, Request $request)
    {
        $distributor = User::where('user_type', 'distributor')
            ->where('id', $id)
            ->firstOrFail();

        // Get the selected category from the request or default to 'all'
        $selectedCategory = $request->get('category', 'all');

        // Get all categories
        $categories = Category::all();

        // Base query for products
        $productsQuery = Product::where('distributor_id', $id)
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0);

        // Filter by category if one is selected
        if ($selectedCategory !== 'all') {
            $productsQuery->where('category_id', $selectedCategory);
        }

        // Get the products with pagination
        $products = $productsQuery->paginate(12);

        return view('retailers.distributor-page', [
            'distributor' => $distributor,
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory
        ]);
    }
}
