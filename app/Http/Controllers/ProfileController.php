<?php

namespace App\Http\Controllers;


use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\RetailerProfile;
use Yajra\Address\Entities\Region;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        return view('retailers.profile.edit', compact('user'));
    }


    public function settings(Request $request): View
    {
        return view('retailers.profile.settings', [
            'user' => $request->user(),
        ]);
    }
    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email'       => 'required|email|max:255',
        ]);

        $user = $request->user();
        $user->update($validated);

        return back()->with('success', 'Profile information updated successfully!');
    }

    /**
     * Update the retailer's profile information.
     */
    public function updateRetailerProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'business_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:15'],
            'city' => ['required', 'string', 'max:10'],
            'province' => ['required', 'string', 'max:10'],
            'region' => ['required', 'string', 'max:10'],
            'barangay' => ['nullable', 'string', 'max:20'],
            'street' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        $retailerProfile = $request->user()->retailerProfile;

        if (!$retailerProfile) {
            $retailerProfile = new RetailerProfile();
            $retailerProfile->user_id = $request->user()->id;
        }

        $retailerProfile->business_name = $request->business_name;
        $retailerProfile->phone = $request->phone;
        $retailerProfile->city = $request->city;
        $retailerProfile->province = $request->province;
        $retailerProfile->region = $request->region;
        $retailerProfile->barangay = $request->barangay;
        $retailerProfile->street = $request->street;

        if ($request->hasFile('profile_picture')) {
            // Delete the old picture if it exists
            if ($retailerProfile->profile_picture) {
                Storage::disk('public')->delete($retailerProfile->profile_picture);
            }
            // Create a custom file name
            $fileName = time() . '_' . $request->user()->id . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            // Store the file in the "profile_pictures" folder in the "public" disk
            $path = $request->file('profile_picture')->storeAs('profile_pictures', $fileName, 'public');
            $retailerProfile->profile_picture = $path;
        }

        $retailerProfile->save();

        // Update profile_completed status to true
        $user = $request->user();
        $user->profile_completed = true;
        $user->save();

        return back()->with('success', 'Retailer profile updated successfully!');
    }


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

        return Redirect::to('/');
    }

    public function downloadCredential()
    {
        // Get the authenticated user's credential
        $credential = Auth::user()->credential;

        // Check if the user has a credential
        if ($credential) {
            // Get the full path of the file
            $filePath = storage_path('app/public/' . $credential->file_path);

            // Check if the file exists
            if (file_exists($filePath)) {
                // Get the file extension from the original file
                $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

                // Create a custom file name for download (e.g., Credential_JohnDoe.pdf)
                $fileName = 'Credential_' . Auth::user()->name . '.' . $fileExtension;

                // Return the file for download with the custom name
                return response()->download($filePath, $fileName);
            }
        }

        // If no file or file doesn't exist
        return redirect()->back()->with('error', 'File not found or inaccessible.');
    }
}
