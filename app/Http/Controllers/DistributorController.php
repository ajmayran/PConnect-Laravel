<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distributors;
use App\Models\Category; // Import the Category model
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

    public function approve($id)
    {
        $distributor = Distributors::findOrFail($id);
        $distributor->approval_status = 'approved'; // Assuming you have an approval_status field
        $distributor->save();

        return redirect()->route('distributors.dashboard')->with('status', 'Distributor account approved successfully.');
    }
}
