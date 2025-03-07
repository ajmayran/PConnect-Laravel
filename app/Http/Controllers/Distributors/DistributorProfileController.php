<?php

namespace App\Http\Controllers\Distributors;

use App\Models\User;
use App\Models\Distributors;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'region' => 'nullable|string|max:10',
            'province' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:10',
            'barangay' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'company_phone_number' => 'required|string|max:15',
        ]);


        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in to set up your profile.');
        }

        $imagePath = null;
        if ($request->hasFile('company_profile_image')) {
            // Store image in distributors folder
            $imagePath = $request->file('company_profile_image')->store('distributors', 'public');

            // If updating and old image exists, delete it
            if ($user->distributor && $user->distributor->company_profile_image) {
                Storage::disk('public')->delete($user->distributor->company_profile_image);
            }
        }


        if (!$user->distributor) {
            // Create a new distributor record with all required fields
            $distributor = new Distributors();
            $distributor->user_id = $user->id;
            $distributor->company_name = $request->company_name;
            $distributor->company_email = $request->company_email;
            $distributor->region = $request->region;
            $distributor->province = $request->province;
            $distributor->city = $request->city;
            $distributor->barangay = $request->barangay;
            $distributor->street = $request->street;
            $distributor->company_phone_number = $request->company_phone_number;
            $distributor->company_profile_image = $imagePath;
            $distributor->save();
        } else {
            $distributor = $user->distributor;
            $distributor->update($request->only([
                'company_name',
                'company_email',
                'region',
                'province',
                'city',
                'barangay',
                'street',
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

        return redirect()->route('distributors.dashboard')->with('success', 'Welcome to PConnect, Distributor!.')->withInput();
    }
}
