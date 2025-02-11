<?php

namespace App\Http\Controllers\Distributors;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InventoryController extends Controller
{
    public function index()
    {
        return view('distributors.inventory'); // Create this view file later
    }
}
