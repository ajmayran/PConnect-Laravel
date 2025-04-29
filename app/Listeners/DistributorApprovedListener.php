<?php

namespace App\Listeners;

use App\Events\DistributorApproved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class DistributorApprovedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\DistributorApproved  $event
     * @return void
     */
    public function handle(DistributorApproved $event)
    {
        $distributor = $event->distributor;
        
        // Mark that this distributor should see the subscription page on next login
        $distributor->has_seen_subscription_page = false;
        $distributor->save();
        
        Log::info('Distributor approved, subscription page flag set', [
            'distributor_id' => $distributor->id,
            'email' => $distributor->email
        ]);
    }
}