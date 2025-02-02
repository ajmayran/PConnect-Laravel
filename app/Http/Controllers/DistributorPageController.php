<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DistributorPageController extends Controller
{
    public function show($id)
    {
        return view('retailer.distributor-page');
    }
}
