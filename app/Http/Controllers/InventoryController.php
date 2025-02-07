<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        return view('distributors.inventory'); // Create this view file later
    }
}
