<?php

namespace App\Http\Controllers\Distributors;

use App\Models\User;
use App\Models\Distributors;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DistributorProfileController extends Controller
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


        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in to set up your profile.');
        }

        $imagePath = null;
        if ($request->hasFile('company_profile_image')) {
            $imagePath = $request->file('company_profile_image')->store('profile-images', 'public');
        }

        if (!$user->distributor) {
            // Create a new distributor record with all required fields
            $distributor = new Distributors();
            $distributor->user_id = $user->id;
            $distributor->company_name = $request->company_name;
            $distributor->company_email = $request->company_email;
            $distributor->company_address = $request->company_address;
            $distributor->company_phone_number = $request->company_phone_number;
            $distributor->company_profile_image = $imagePath;
            $distributor->save();
        } else {
            $distributor = $user->distributor;
            $distributor->update($request->only([
                'company_name',
                'company_email',
                'company_address',
                'company_phone_number',
            ]));

            if ($imagePath) {
                $distributor->company_profile_image = $imagePath;
                $distributor->save();
            }
        }

        DB::table('users')
            ->where('id', Auth::id())
            ->update(['profile_completed' => true]);

        return redirect()->route('distributors.dashboard')->with('status', 'Profile setup completed successfully.')->withInput();
    }
}
