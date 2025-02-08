<?php

namespace App\Http\Controllers\Retailers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Distributors; // Import the Distributor model
use App\Http\Controllers\DistributorController;

class RetailerDashboardController extends Controller
{
    public function index()
    {
        $distributors = Distributors::all(); // Fetch all distributors
        $products = Product::paginate(10); // Fetch products with pagination

        return view('retailers.dashboard', [
            'user' => Auth::user(),
            'distributors' => $distributors,
            'products' => $products,
        ]);
        
    }
}
