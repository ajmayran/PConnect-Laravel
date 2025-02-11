<?php

namespace App\Http\Controllers\Distributors;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InsightsController extends Controller
{
    public function index()
    {
        return view('distributors.insights'); // Create this view file later
    }
}
