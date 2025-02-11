<?php

namespace App\Http\Controllers\Retailers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductDescController extends Controller
{
    public function show()
    {
        return view('retailers.product-description');
    }
}
