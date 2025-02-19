<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RetailerProfile;

class AllRetailerController extends Controller
{
    public function allRetailers()
    {
        $retailerProfiles = RetailerProfile::with('user')->whereHas('user', function ($query) {
            $query->where('user_type', 'retailer');
        })->get();

        return view('admin.retailers.all', compact('retailerProfiles'));
    }
}