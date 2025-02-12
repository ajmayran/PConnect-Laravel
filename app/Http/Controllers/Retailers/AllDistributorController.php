<?php

namespace App\Http\Controllers\Retailers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distributors;


class AllDistributorController extends Controller
{
    public function index()
    {
        $distributors = Distributors::all();
        return view('retailers.all-distributor',[
            'distributors' => $distributors,
        ]);
    }
}
