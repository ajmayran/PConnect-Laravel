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

    public function updateRetailerProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'business_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{11}$/'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);
    
        $retailerProfile = $request->user()->retailerProfile;
    
        if (!$retailerProfile) {
            $retailerProfile = new RetailerProfile();
            $retailerProfile->user_id = $request->user()->id;
        }
    
        $retailerProfile->business_name = $request->business_name;
        $retailerProfile->phone = $request->phone;
    
        if ($request->hasFile('profile_picture')) {
            // Delete the old picture if it exists
            if ($retailerProfile->profile_picture) {
                Storage::disk('public')->delete($retailerProfile->profile_picture);
            }
            // Create a custom file name
            $fileName = time() . $request->user()->id . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            // Store the file in the "profile_pictures" folder in the "public" disk with custom filename
            $path = Storage::disk('public')->putFileAs('retailers_profile', $request->file('profile_picture'), $fileName);
            $retailerProfile->profile_picture = $path;
        }
    
        $retailerProfile->save();
    
        // Update profile_completed status if business name and phone are filled
        $user = $request->user();
        $user->profile_completed = !empty($retailerProfile->business_name) && !empty($retailerProfile->phone);
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

    public function checkProfileComplete()
    {
        $user = Auth::user();
        $isComplete = false;
    
        if ($user->user_type === 'retailer' && $user->retailerProfile) {
            // Define what constitutes a complete profile
            $requiredProfileFields = [
                'business_name',
                'phone'
            ];
            
            // Check profile fields
            $profileComplete = true;
            foreach ($requiredProfileFields as $field) {
                if (empty($user->retailerProfile->$field)) {
                    $profileComplete = false;
                    break;
                }
            }
            
            // Check if there's a default address with required fields
            if ($profileComplete) {
                $address = $user->retailerProfile->defaultAddress;
                
                if (!$address || empty($address->barangay) || empty($address->street)) {
                    $profileComplete = false;
                }
            }
    
            $isComplete = $profileComplete;
        }
    
        return response()->json([
            'isComplete' => $isComplete
        ]);
    }
}
