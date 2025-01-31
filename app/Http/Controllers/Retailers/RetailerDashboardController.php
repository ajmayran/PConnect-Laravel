<?php

namespace App\Http\Controllers\Retailers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RetailerDashboardController extends Controller
{
    public function index()
    {
        return view('retailers.dashboard', ['user' => Auth::user()]);
    }
}
