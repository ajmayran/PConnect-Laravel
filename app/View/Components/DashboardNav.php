<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DashboardNav extends Component
{
    public function __construct()
    {
        //
    }

    public function render()
    {
        return view('components.retailer-topnav');
    }
}