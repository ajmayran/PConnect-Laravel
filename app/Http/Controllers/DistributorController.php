<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Distributors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistributorController extends Controller
{
    public function index()
    {
        $distributors = Distributors::with('user')->get();
        $products = Product::with('distributor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('retailers.dashboard', compact('distributors', 'products'));
    }
    public function show($id)
    {
        $distributor = Distributors::with(['products', 'products.category'])->findOrFail($id);
        $categories = Category::all();
        $selectedCategory = request('category', 'all');

        $products = $distributor->products;
        if ($selectedCategory !== 'all') {
            $products = $products->where('category_id', $selectedCategory);
        }

        return view('retailers.distributor-page', compact('distributor', 'categories', 'products', 'selectedCategory'));
    }
    public function showProducts($id)
    {
        $distributor = Distributors::findOrFail($id); // Fetch the distributor
        $products = $distributor->products()->paginate(10); // Fetch products with pagination

        return view('distributors.products.index', compact('distributor', 'products')); // Pass data to the view
    }

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
}
