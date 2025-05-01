<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use App\View\Components\Label;
use App\View\Components\Input;
use Illuminate\Support\ServiceProvider;
use App\View\Components\ProfileCompletionAlert;
use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Events\DistributorApproved;
use App\Listeners\DistributorApprovedListener;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        config(['broadcasting.default' => 'pusher']);
        Paginator::defaultView('vendor.pagination.tailwind');
        Blade::component('profile-completion-alert', ProfileCompletionAlert::class);
        Product::observe(\App\Observers\ProductObserver::class);

        $this->registerEventListeners();
    }


    private function registerEventListeners()
    {
        // Listen for DistributorApproved event
        Event::listen(
            DistributorApproved::class,
            [DistributorApprovedListener::class, 'handle']
        );

        // Listen for Login event
        Event::listen(
            Login::class,
            [\App\Listeners\DistributorLoginListener::class, 'handle']
        );
    }
}
