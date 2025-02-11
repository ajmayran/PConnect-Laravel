<?php

namespace App\Http\Controllers\Retailers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DistributorPageController extends Controller
{
    public function show()
    {
        return view('retailers.distributor-page');
    }
}
