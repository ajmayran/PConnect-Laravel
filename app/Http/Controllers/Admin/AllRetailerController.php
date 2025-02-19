<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RetailerProfile;

class AllRetailerController extends Controller
{
    public function allRetailers()
    {
        $retailerProfiles = RetailerProfile::with('user')->get();
        return view('admin.retailers.all', compact('retailerProfiles'));
    }
}