<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class settingController extends Controller
{
    public function settings()
{
    return view('admin.settings'); // Ensure this matches the path to your settings Blade file
}
}
