<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Yajra\Address\Entities\Barangay;
use Illuminate\Database\Eloquent\Model;

class RetailerProfile extends Model
{
    protected $table = 'retailer_profile'; // Ensure this matches your database table name

    protected $fillable = [
        'user_id',
        'business_name',
        'phone',
        'address',
        'profile_picture',
    ];

    // Maximum number of addresses a retailer can have
    const MAX_ADDRESSES = 3;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function defaultAddress()
    {
        return $this->morphOne(Address::class, 'addressable')->where('is_default', true);
    }

    public function getFormattedAddresses()
    {
        return $this->addresses->map(function ($address) {
            $address->barangay_name = $address->getBarangayNameAttribute();
            return $address;
        });
    }

    public function addAddress(array $addressData, bool $makeDefault = false)
    {
        // Check if the retailer already has the maximum number of addresses
        if ($this->addresses()->count() >= self::MAX_ADDRESSES) {
            return null; // Maximum addresses reached
        }

        // If making this address default, remove default flag from other addresses
        if ($makeDefault) {
            $this->addresses()->update(['is_default' => false]);
        }
        // If this is the first address, make it default regardless
        else if ($this->addresses()->count() === 0) {
            $makeDefault = true;
        }

        // Create a new address
        $address = new Address(array_merge($addressData, [
            'is_default' => $makeDefault,
            'label' => $addressData['label'] ?? 'Address ' . ($this->addresses()->count() + 1)
        ]));

        $this->addresses()->save($address);
        return $address;
    }

    public function updateAddress(int $addressId, array $addressData, bool $makeDefault = false)
    {
        $address = $this->addresses()->find($addressId);
        
        if (!$address) {
            return null;
        }

        // If making this address default, remove default flag from other addresses
        if ($makeDefault && !$address->is_default) {
            $this->addresses()->update(['is_default' => false]);
            $address->is_default = true;
        }

        $address->update($addressData);
        return $address;
    }

    public function removeAddress(int $addressId)
    {
        $address = $this->addresses()->find($addressId);
        
        if (!$address || $address->is_default) {
            return false; // Can't delete default address
        }

        return $address->delete();
    }

    public function setDefaultAddress(int $addressId)
    {
        $address = $this->addresses()->find($addressId);
        
        if (!$address) {
            return false;
        }

        // Remove default flag from all addresses
        $this->addresses()->update(['is_default' => false]);
        
        // Set the specified address as default
        $address->is_default = true;
        $address->save();
        
        return true;
    }

    public function canAddMoreAddresses()
    {
        return $this->addresses()->count() < self::MAX_ADDRESSES;
    }

    public function remainingAddressSlots()
    {
        return max(0, self::MAX_ADDRESSES - $this->addresses()->count());
    }
}