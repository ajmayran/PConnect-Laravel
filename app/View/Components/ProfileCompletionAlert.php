<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class ProfileCompletionAlert extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public $user)
    {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.profile-completion-alert');
    }
}