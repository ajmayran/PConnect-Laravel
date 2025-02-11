<?php

namespace App\Http\Controllers\Distributors;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MessageController extends Controller
{
    public function index()
    {
        return view('distributors.messages'); // Return the existing messages view

    }
}
