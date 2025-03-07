<?php

namespace App\Http\Controllers\Distributors;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReturnController extends Controller
{
    public function index()
    {
        return view('distributors.returns'); // Create this view file later
    }
}
