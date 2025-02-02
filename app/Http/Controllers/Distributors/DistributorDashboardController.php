<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistributorDashboardController extends Controller
{
    public function index()
    {
        return view('distributors.dashboard', ['user' => Auth::user()]);
    }
}
