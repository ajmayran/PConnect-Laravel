<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RetailerDistributorPageController extends Controller
{
    public function index()
    {
        $distributors = [
            [
                'name' => 'Jacob Trading',
                'image' => 'img/Distributors/alaska.png',
                'description' => 'A brief description of the distributor company and what they offer. Providing quality products and excellent service.',
                'location' => 'City, Country'
            ],
            [
                'name' => 'Zambasulta',
                'image' => 'img/Distributors/ph.png',
                'description' => 'This distributor specializes in delivering the best products to enhance your experience. Always committed to quality.',
                'location' => 'City, Country'
            ]
        ];

        return view('retailers.nav-distributor', compact('distributors'));
    }
}