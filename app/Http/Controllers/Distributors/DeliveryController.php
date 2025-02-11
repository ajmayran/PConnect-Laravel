<?php

namespace App\Http\Controllers\Distributors;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DeliveryController extends Controller
{
    public function index()
    {
        return view('distributors.delivery.index'); // Create this view file later
    }
}
