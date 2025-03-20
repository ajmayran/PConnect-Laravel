<?php

namespace App\Http\Controllers\Distributors;

use App\Models\User;
use App\Models\Distributors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use view;

class DistributorProfileController extends Controller
{

    /**
     * Display the distributor's profile setup view.
     */
    public function setup()
    {
        return view('distributors.setup');
    }

    /**
     * Update the distributor's initial profile setup.
     */
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
            'company_phone_number' => 'required|numeric|digits:11',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in to set up your profile.');
        }

        $imagePath = null;
        if ($request->hasFile('company_profile_image')) {
            // Store image in distributors folder
            $fileName = time() . $user->id . '.' . $request->file('company_profile_image')->getClientOriginalExtension();
            $imagePath = $request->file('company_profile_image')->storeAs('distributors_profile', $fileName, 'public');

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

        return redirect()->route('distributors.dashboard')->with('success', 'Welcome to PConnect, Distributor!');
    }

    /**
     * Display the distributor's profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();

        if ($user->distributor && $user->distributor->barangay) {
            // Fetch barangay name from DB if needed
            $barangay = DB::table('barangays')->where('code', $user->distributor->barangay)->first();
            if ($barangay) {
                $user->distributor->barangay_name = $barangay->name;
            } else {
                $user->distributor->barangay_name = 'Unknown';
            }
        }

        return view('distributors.profile.edit', compact('user'));
    }

    /**
     * Update the distributor's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $user = $request->user();
        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the distributor company profile information.
     */
    public function updateDistributorProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'email', 'max:255'],
            'region' => ['nullable', 'string', 'max:10'],
            'province' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:10'],
            'barangay' => ['nullable', 'string', 'max:20'],
            'street' => ['nullable', 'string', 'max:255'],
            'company_phone_number' => ['required', 'numeric', 'max:11'],
            'company_profile_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $distributorProfile = $request->user()->distributor;

        if (!$distributorProfile) {
            $distributorProfile = new Distributors();
            $distributorProfile->user_id = $request->user()->id;
        }

        $distributorProfile->company_name = $request->company_name;
        $distributorProfile->company_email = $request->company_email;
        $distributorProfile->region = $request->region;
        $distributorProfile->province = $request->province;
        $distributorProfile->city = $request->city;
        $distributorProfile->barangay = $request->barangay;
        $distributorProfile->street = $request->street;
        $distributorProfile->company_phone_number = $request->company_phone_number;

        if ($request->hasFile('company_profile_image')) {
            // Delete the old picture if it exists
            if ($distributorProfile->company_profile_image) {
                Storage::disk('public')->delete($distributorProfile->company_profile_image);
            }
         
            $fileName = time() . $request->user()->id . '.' . $request->file('company_profile_image')->getClientOriginalExtension();
            // Store the file in the "distributors" folder in the "public" disk
            $path = Storage::disk('public')->putFileAs('distributors_profile', $request->file('company_profile_image'), $fileName);
            $distributorProfile->company_profile_image = $path;
        }

        $distributorProfile->save();

        // Update profile_completed status to true
        $user = $request->user();
        $user->profile_completed = true;
        $user->save();

        return back()->with('success', 'Distributor profile updated successfully!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
