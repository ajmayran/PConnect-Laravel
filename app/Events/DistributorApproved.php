<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DistributorApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $distributor;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\User  $distributor
     * @return void
     */
    public function __construct(User $distributor)
    {
        $this->distributor = $distributor;
    }
}