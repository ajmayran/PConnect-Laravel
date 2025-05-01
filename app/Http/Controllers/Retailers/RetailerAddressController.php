<?php

namespace App\Http\Controllers\Retailers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\RetailerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class RetailerAddressController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $addresses = $user->retailerProfile->getFormattedAddresses();
        
        // Get barangays for the select dropdown
        $barangays = DB::table('barangays')
            ->where('city_id', '093170')
            ->orderBy('name')
            ->get();
            
        return view('retailers.address.index', compact('user', 'addresses', 'barangays'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $retailerProfile = $user->retailerProfile;
        
        // Use canAddMoreAddresses() from the model
        if (!$retailerProfile->canAddMoreAddresses()) {
            return back()->with('error', 'You can only have up to ' . RetailerProfile::MAX_ADDRESSES . ' delivery addresses.');
        }
        
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'barangay' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'is_default' => 'sometimes|boolean',
        ]);
        
        // Set region, province, and city to default values for Zamboanga City
        $validated['region'] = '09';
        $validated['province'] = '097300';
        $validated['city'] = '093170';
        
        // If this is the first address or default is checked, make it default
        $isDefault = $request->has('is_default') || $retailerProfile->addresses()->count() === 0;
        
        // Use the model method to add address
        $address = $retailerProfile->addAddress($validated, $isDefault);
        
        if (!$address) {
            return back()->with('error', 'Failed to add address. Maximum limit reached.');
        }
        
        return back()->with('success', 'Address added successfully!');
    }
    
    public function update(Request $request, Address $address): RedirectResponse
    {
        $user = Auth::user();
        $retailerProfile = $user->retailerProfile;
        
        // Make sure the address belongs to the authenticated user
        if ($address->addressable_id !== $retailerProfile->id) {
            return back()->with('error', 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'barangay' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'is_default' => 'sometimes|boolean',
        ]);
        
        // Use the model method to update address
        $updatedAddress = $retailerProfile->updateAddress($address->id, $validated, $request->has('is_default'));
        
        if (!$updatedAddress) {
            return back()->with('error', 'Failed to update address.');
        }
        
        return back()->with('success', 'Address updated successfully!');
    }
    
    public function destroy(Address $address): RedirectResponse
    {
        $user = Auth::user();
        $retailerProfile = $user->retailerProfile;
        
        // Make sure the address belongs to the authenticated user
        if ($address->addressable_id !== $retailerProfile->id) {
            return back()->with('error', 'Unauthorized action.');
        }
        
        // Use the model method to remove address
        $result = $retailerProfile->removeAddress($address->id);
        
        if (!$result) {
            return back()->with('error', 'Cannot delete the default address. Make another address default first.');
        }
        
        return back()->with('success', 'Address deleted successfully!');
    }
    
    public function setDefault(Address $address): RedirectResponse
    {
        $user = Auth::user();
        $retailerProfile = $user->retailerProfile;
        
        // Make sure the address belongs to the authenticated user
        if ($address->addressable_id !== $retailerProfile->id) {
            return back()->with('error', 'Unauthorized action.');
        }
        
        // Use the model method to set default address
        $result = $retailerProfile->setDefaultAddress($address->id);
        
        if (!$result) {
            return back()->with('error', 'Failed to set default address.');
        }
        
        return back()->with('success', 'Default address updated successfully!');
    }
}