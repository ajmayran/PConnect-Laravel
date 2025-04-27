<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $senderId;
    public $receiverId;

    public function __construct($message, $senderId, $receiverId)
    {
        $this->message = $message;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->receiverId),
        ];
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'senderId' => $this->senderId,
            'receiverId' => $this->receiverId,
            'time' => now()->toDateTimeString(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}