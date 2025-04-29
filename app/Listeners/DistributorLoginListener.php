<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class DistributorLoginListener
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;

        if ($user->user_type === 'distributor' && $user->status === 'approved') {

            // Make sure we're working with the Eloquent model
            if (!method_exists($user, 'update')) {
                $user = \App\Models\User::find($user->id);
                if (!$user) {
                    Log::error('Failed to retrieve user model', [
                        'user_id' => $event->user->id ?? null
                    ]);
                    return;
                }
            }

            // Clear all redirect flags to avoid stale data
            Session::forget(['redirect_to_setup', 'show_subscription']);

            // If profile not completed, flag for profile setup redirect
            if (!$user->profile_completed) {
                Log::info('Distributor needs profile setup', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                Session::put('redirect_to_setup', true);
            }

            // If profile completed but hasn't seen subscription page
            elseif (!$user->has_seen_subscription_page) {
                Log::info('Distributor profile completed, show subscription page', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                // Mark as seen
                $user->has_seen_subscription_page = true;
                $user->save();

                Session::put('show_subscription', true);
            }
        }
    }
}
