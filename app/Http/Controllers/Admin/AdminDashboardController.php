<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Distributors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', ['user' => Auth::user()]);
    }

}
