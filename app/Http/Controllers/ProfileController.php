<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distributors;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Existing methods...

    public function setup()
    {
        return view('distributors.setup'); // Return the setup view
    }

    public function updateSetup(Request $request)
    {
        $request->validate([
            'company_profile_image' => 'nullable|image|max:2048',
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_address' => 'required|string|max:255',
            'company_phone_number' => 'required|string|max:15',
        ]);

        $distributor = Auth::user()->distributor;
        $distributor->update($request->only([
            'company_profile_image',
            'company_name',
            'company_email',
            'company_address',
            'company_phone_number',
        ]));

        $distributor->profile_completed = true; // Mark profile as completed
        $distributor->save();

        return redirect()->route('distributors.dashboard')->with('status', 'Profile setup completed successfully.');
    }
}
