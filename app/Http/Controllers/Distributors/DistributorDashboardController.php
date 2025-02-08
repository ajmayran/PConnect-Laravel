<?php

namespace App\Http\Controllers\Distributors;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DistributorDashboardController extends Controller
{
    public function index()
    {
        return view('distributors.dashboard', ['user' => Auth::user()]);
    }

    public function dashboard()
    {
        return view('distributors.dashboard', ['user' => Auth::user()]);
    }
    
}
