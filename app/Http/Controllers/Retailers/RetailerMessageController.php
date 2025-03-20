<?php

namespace App\Http\Controllers\Retailers;

use App\Models\User;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            broadcast(new MessageSent($message->message, Auth::id(), $request->receiver_id))->toOthers();

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
        try {
            // Get authenticated user ID
            $userId = Auth::id();

            // Get the distinct sender IDs for debugging
            $distinctSenders = Message::where('receiver_id', $userId)
                ->where('is_read', false)
                ->select('sender_id')
                ->distinct()
                ->get()
                ->pluck('sender_id');

            // Count unique senders who have sent unread messages
            $unreadSendersCount = $distinctSenders->count();

            // For debugging: Get total unread messages too
            $totalUnreadCount = Message::where('receiver_id', $userId)
                ->where('is_read', false)
                ->count();

            // Log the values for debugging
            Log::debug("Unread senders: " . $distinctSenders->join(', '));
            Log::debug("Unread senders count: $unreadSendersCount, Total unread messages: $totalUnreadCount");

            return response()->json([
                'success' => true,
                'unread_count' => $unreadSendersCount,
                'count' => $unreadSendersCount,
                'debug_total_messages' => $totalUnreadCount,
                'debug_senders' => $distinctSenders->toArray()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching unread message count: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching unread messages: ' . $e->getMessage(),
                'count' => 0,
                'unread_count' => 0
            ]);
        }
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


    public function getMessagePreviews()
    {
        try {
            // Get the latest message from each unique sender
            $latestMessages = Message::where('receiver_id', Auth::id())
                ->select('sender_id', DB::raw('MAX(id) as max_id'))
                ->groupBy('sender_id')
                ->orderBy('max_id', 'desc')
                ->take(5)
                ->get();

            $messageIds = $latestMessages->pluck('max_id');

            // Fetch the actual message details
            $messages = Message::whereIn('id', $messageIds)
                ->with('sender:id,first_name,last_name')
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedMessages = $messages->map(function ($message) {
                $senderImg = null;
                if ($message->sender->distributorProfile && $message->sender->distributorProfile->profile_picture) {
                    $senderImg = asset('storage/' . $message->sender->distributorProfile->profile_picture);
                }

                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->first_name . ' ' . $message->sender->last_name,
                    'sender_image' => $senderImg,
                    'message' => \Illuminate\Support\Str::limit($message->message, 50),
                    'created_at' => $message->created_at,
                    'is_read' => $message->is_read,
                ];
            });

            return response()->json([
                'success' => true,
                'messages' => $formattedMessages
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching message previews: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching message previews',
            ], 500);
        }
    }

    public function show($userId)
    {
        return redirect()->route('retailers.messages.index', ['distributor' => $userId]);
    }
}
