<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class RetailerSidebar extends Component
{
    public $user;
    
    public function __construct()
    {
        $this->user = Auth::user();
    }
    
    public function render()
    {
        return view('components.retailer-sidebar');
    }
}