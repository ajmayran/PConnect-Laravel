<?php

namespace App\Http\Controllers\Retailers;

use App\Models\User;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RetailerMessageController extends Controller
{
    public function index(Request $request)
    {
        // Get distributors with their latest messages
        $distributors = User::where('user_type', 'distributor')
            ->where('status', 'approved')
            ->with('distributor')
            ->get();

        // Get latest message and unread count for each distributor
        foreach ($distributors as $distributor) {
            // Get latest message between retailer and this distributor
            $latestMessage = Message::where(function ($query) use ($distributor) {
                $query->where('sender_id', Auth::id())
                    ->where('receiver_id', $distributor->id);
            })
                ->orWhere(function ($query) use ($distributor) {
                    $query->where('sender_id', $distributor->id)
                        ->where('receiver_id', Auth::id());
                })
                ->latest()
                ->first();

            // Get unread count for this distributor
            $unreadCount = Message::where('sender_id', $distributor->id)
                ->where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->count();

            $distributor->latestMessage = $latestMessage;
            $distributor->unreadCount = $unreadCount;
        }

        // Sort distributors by latest message time (most recent first)
        $distributors = $distributors->sortByDesc(function ($distributor) {
            return $distributor->latestMessage ? $distributor->latestMessage->created_at : null;
        })->values();

        // Get current conversation if any distributor is selected
        $currentDistributorId = $request->query('distributor');
        $messages = collect();
        $currentDistributor = null;

        if ($currentDistributorId) {
            $currentDistributor = User::with('distributor')->find($currentDistributorId);
            if ($currentDistributor) {
                $messages = $this->getConversation($currentDistributorId);

                // Mark messages as read
                Message::where('sender_id', $currentDistributorId)
                    ->where('receiver_id', Auth::id())
                    ->where('is_read', false)
                    ->update(['is_read' => true, 'read_at' => now()]);
            }
        }

        return view('retailers.messages.index', compact('distributors', 'messages', 'currentDistributor'));
    }

    public function getConversation($distributorId)
    {
        return Message::where(function ($query) use ($distributorId) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $distributorId);
        })
            ->orWhere(function ($query) use ($distributorId) {
                $query->where('sender_id', $distributorId)
                    ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        try {
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'is_read' => false,
            ]);

            // Broadcast the message
            event(new MessageSent($message->message, Auth::id(), $request->receiver_id));

            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    public function getUnreadCount()
    {
        $unreadCount = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:users,id',
        ]);

        // Mark all messages from this sender as read
        Message::where('sender_id', $request->sender_id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['status' => 'success']);
    }
}
