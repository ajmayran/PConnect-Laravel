<?php

namespace App\Http\Controllers\Distributors;

use view;
use App\Models\User;
use App\Models\Address;
use App\Models\Distributors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

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
            $distributor->company_phone_number = $request->company_phone_number;
            $distributor->company_profile_image = $imagePath;
            $distributor->save();
            
            // Create the address for this distributor
            $address = new Address([
                'region' => $request->region,
                'province' => $request->province,
                'city' => $request->city,
                'barangay' => $request->barangay,
                'street' => $request->street,
                'is_default' => true,
                'label' => 'Company Address'
            ]);
            
            $distributor->addresses()->save($address);
            
        } else {
            $distributor = $user->distributor;
            $distributor->update([
                'company_name' => $request->company_name,
                'company_email' => $request->company_email,
                'company_phone_number' => $request->company_phone_number,
            ]);

            if ($imagePath) {
                $distributor->company_profile_image = $imagePath;
                $distributor->save();
            }
            
            // Update or create the address
            $address = $distributor->defaultAddress;
            
            if ($address) {
                $address->update([
                    'region' => $request->region,
                    'province' => $request->province,
                    'city' => $request->city,
                    'barangay' => $request->barangay,
                    'street' => $request->street,
                ]);
            } else {
                $address = new Address([
                    'region' => $request->region,
                    'province' => $request->province,
                    'city' => $request->city,
                    'barangay' => $request->barangay,
                    'street' => $request->street,
                    'is_default' => true,
                    'label' => 'Company Address'
                ]);
                
                $distributor->addresses()->save($address);
            }
        }

        DB::table('users')
        ->where('id', Auth::id())
        ->update(['profile_completed' => true]);
    
        // Refresh user model to get updated values from database
        $user = User::find($user->id);

        // When the profile is completed, redirect to subscription page if they haven't seen it yet
        if (!$user->has_seen_subscription_page) {
            // Mark the user as having seen the subscription page
            $user->has_seen_subscription_page = true;
            $user->save();

            return redirect()->route('distributors.subscription')
                ->with('success', 'Your profile has been completed! Now choose a subscription plan.');
        }

        return redirect()->route('distributors.dashboard')->with('success', 'Welcome to PConnect, Distributor!');
    }

    /**
     * Display the distributor's profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        
        // Get the distributor's address
        if ($user->distributor) {
            $address = $user->distributor->defaultAddress;
            
            if ($address && $address->barangay) {
                // Fetch barangay name from DB
                $barangay = DB::table('barangays')->where('code', $address->barangay)->first();
                if ($barangay) {
                    $address->barangay_name = $barangay->name;
                } else {
                    $address->barangay_name = 'Unknown';
                }
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
            'company_phone_number' => ['required', 'numeric', 'digits:11'],
            'company_profile_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $distributorProfile = $request->user()->distributor;

        if (!$distributorProfile) {
            $distributorProfile = new Distributors();
            $distributorProfile->user_id = $request->user()->id;
        }

        $distributorProfile->company_name = $request->company_name;
        $distributorProfile->company_email = $request->company_email;
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
        
        // Update or create address
        $address = $distributorProfile->defaultAddress;
        
        if ($address) {
            $address->update([
                'region' => $request->region,
                'province' => $request->province,
                'city' => $request->city,
                'barangay' => $request->barangay,
                'street' => $request->street,
            ]);
        } else {
            $address = new Address([
                'region' => $request->region,
                'province' => $request->province,
                'city' => $request->city,
                'barangay' => $request->barangay,
                'street' => $request->street,
                'is_default' => true,
                'label' => 'Company Address'
            ]);
            
            $distributorProfile->addresses()->save($address);
        }

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


    public function ordersSettings()
    {
        $user = Auth::user();
        return view('distributors.profile.orders-settings', compact('user'));
    }

    public function updateOrdersSettings(Request $request)
    {
        $request->validate([
            'cut_off_time' => 'nullable|string',
            'cut_off_hour' => 'nullable|string',
            'cut_off_minute' => 'nullable|string',
            'cut_off_period' => 'nullable|string|in:AM,PM',
            'remove_cut_off_time' => 'nullable|string',
        ]);

        $distributor = Auth::user()->distributor;

        // Debug information before saving
        Log::info('Updating distributor settings - BEFORE', [
            'current_cut_off_time' => $distributor->cut_off_time,
            'remove_cut_off_time' => $request->has('remove_cut_off_time'),
        ]);

        // Check if we should remove the cut-off time
        if ($request->has('remove_cut_off_time')) {
            // Set cut_off_time to NULL
            DB::table('distributors')
                ->where('id', $distributor->id)
                ->update(['cut_off_time' => null]);

            // Refresh the model to get the updated value
            $distributor->refresh();

            Log::info('Cut-off time removed', [
                'updated_cut_off_time' => $distributor->cut_off_time
            ]);
        }
        // Otherwise update the cut-off time if provided
        else if ($request->filled('cut_off_hour') && $request->filled('cut_off_minute') && $request->filled('cut_off_period')) {
            try {
                // Convert hour to 24-hour format
                $hour = (int)$request->cut_off_hour;
                if ($request->cut_off_period === 'PM' && $hour !== 12) {
                    $hour += 12;
                } else if ($request->cut_off_period === 'AM' && $hour === 12) {
                    $hour = 0;
                }

                // Format time string in 24-hour format (HH:MM:SS)
                $timeString = sprintf('%02d:%02d:00', $hour, (int)$request->cut_off_minute);

                Log::info('Constructed time string', [
                    'timeString' => $timeString
                ]);

                // Direct SQL update to ensure proper time format is stored
                DB::table('distributors')
                    ->where('id', $distributor->id)
                    ->update(['cut_off_time' => $timeString]);

                // Refresh the model to get the updated value
                $distributor->refresh();

                Log::info('Time parsed and saved successfully', [
                    'hour' => $hour,
                    'minute' => $request->cut_off_minute,
                    'period' => $request->cut_off_period,
                    'formatted' => $timeString,
                    'from_db' => $distributor->cut_off_time
                ]);
            } catch (\Exception $e) {
                Log::error('Error parsing time value', [
                    'hour' => $request->cut_off_hour,
                    'minute' => $request->cut_off_minute,
                    'period' => $request->cut_off_period,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Update accepting_orders status
        $distributor->accepting_orders = $request->has('accepting_orders') ? true : false;
        $distributor->save();

        // Debug information after saving
        Log::info('Updating distributor settings - AFTER', [
            'updated_cut_off_time' => $distributor->cut_off_time
        ]);

        return redirect()->route('distributors.profile.orders-settings')->with('status', 'orders-settings-updated');
    }
}