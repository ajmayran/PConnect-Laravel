<?php

namespace App\Http\Controllers\Distributors;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DistributorDashboardController extends Controller
{
    public function dashboard()
    {

        if (session()->has('show_subscription')) {
            session()->forget('show_subscription');
            return redirect()->route('distributors.subscription');
        }
        
        return view('distributors.dashboard', ['user' => Auth::user()]);
    }
}
