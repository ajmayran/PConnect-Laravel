<?php

namespace App\Http\Controllers\Distributors;

use App\Models\User;
use App\Models\Order;
use App\Models\Message;
use App\Models\BlockedMessage;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DistributorMessageController extends Controller
{
    public function index()
    {
        $authUserId = Auth::id();

        // Get retailers who have exchanged messages with this distributor
        $retailers = User::where('user_type', 'retailer')
            ->whereHas('retailerProfile')
            ->whereDoesntHave('blockedMessages', function ($query) use ($authUserId) {
                $query->where('distributor_id', $authUserId);
            })
            ->where(function ($query) use ($authUserId) {
                // Either sent messages to this distributor
                $query->whereHas('sentMessages', function ($q) use ($authUserId) {
                    $q->where('receiver_id', $authUserId);
                })
                    // Or received messages from this distributor
                    ->orWhereHas('receivedMessages', function ($q) use ($authUserId) {
                        $q->where('sender_id', $authUserId);
                    });
            })
            ->get();

        // Add unread message count for each retailer
        foreach ($retailers as $retailer) {
            $retailer->unread_count = Message::where('sender_id', $retailer->id)
                ->where('receiver_id', $authUserId)
                ->where('is_read', false)
                ->count();
        }

        // Get current conversation if any retailer is selected
        $currentRetailerId = request()->query('retailer');
        $messages = collect();
        $currentRetailer = null;
        $hasExistingConversation = false;

        if ($currentRetailerId) {
            // Get the retailer regardless of whether they have exchanged messages
            $currentRetailer = User::with('retailerProfile')->find($currentRetailerId);

            // Check if this retailer has exchanged messages with the distributor
            $hasExistingConversation = Message::where(function ($query) use ($authUserId, $currentRetailerId) {
                $query->where('sender_id', $authUserId)
                    ->where('receiver_id', $currentRetailerId);
            })
                ->orWhere(function ($query) use ($authUserId, $currentRetailerId) {
                    $query->where('sender_id', $currentRetailerId)
                        ->where('receiver_id', $authUserId);
                })
                ->exists();

            if ($currentRetailer) {
                // Get existing messages if there are any
                if ($hasExistingConversation) {
                    $messages = $this->getConversation($currentRetailerId);

                    // Mark messages as read
                    Message::where('sender_id', $currentRetailerId)
                        ->where('receiver_id', $authUserId)
                        ->where('is_read', false)
                        ->update(['is_read' => true, 'read_at' => now()]);
                }

                // If the retailer is not in our list yet, add them
                if (!$retailers->contains('id', $currentRetailerId)) {
                    $currentRetailer->unread_count = 0;
                    $currentRetailer->is_new_conversation = true;

                    // Add this retailer to the list so they appear in the sidebar
                    $retailers->prepend($currentRetailer);
                }
            }
        }

        return view('distributors.messages.index', compact(
            'retailers',
            'messages',
            'currentRetailer',
            'hasExistingConversation'
        ));
    }

    public function getConversation($retailerId)
    {
        return Message::where(function ($query) use ($retailerId) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $retailerId);
        })
            ->orWhere(function ($query) use ($retailerId) {
                $query->where('sender_id', $retailerId)
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

        $senderId = Auth::id();
        $receiverId = $request->receiver_id;

        // Check if messages from this retailer are blocked
        $isBlocked = BlockedMessage::where('distributor_id', $senderId)
            ->where('retailer_id', $receiverId)
            ->exists();

        if ($isBlocked) {
            return response()->json([
                'success' => false,
                'message' => 'You have blocked messages from this retailer. Please unblock them to send messages.'
            ], 403);
        }

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

        Message::where('sender_id', $request->sender_id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['status' => 'success']);
    }

    public function show($retailerId)
    {
        // Check if the retailerId is valid
        $retailer = User::where('id', $retailerId)
            ->where('user_type', 'retailer')
            ->first();

        if (!$retailer) {
            return redirect()->route('distributors.messages.index')
                ->with('error', 'Retailer not found.');
        }

        // Redirect to messages index with retailer parameter
        return redirect()->route('distributors.messages.index', ['retailer' => $retailerId]);
    }

    public function blockedMessages()
    {
        $distributorId = Auth::id();

        $blockedMessages = BlockedMessage::where('distributor_id', $distributorId)
            ->with('retailer.retailerProfile')
            ->paginate(10);

        return view('distributors.blocking.blocked-messages', compact('blockedMessages'));
    }

    public function toggleBlockMessages(Request $request, $retailerId)
    {
        $distributorId = Auth::id();

        $existingBlock = BlockedMessage::where('distributor_id', $distributorId)
            ->where('retailer_id', $retailerId)
            ->first();

        if ($existingBlock) {
            // Unblock
            $existingBlock->delete();
            $message = "Messages from this retailer have been unblocked.";
        } else {
            // Block
            BlockedMessage::create([
                'distributor_id' => $distributorId,
                'retailer_id' => $retailerId,
                'reason' => $request->input('reason', 'Blocked by distributor')
            ]);
            $message = "Messages from this retailer have been blocked.";
        }

        return redirect()->back()->with('success', $message);
    }

    public function unblockMessages($blockId)
    {
        $block = BlockedMessage::where('id', $blockId)
            ->where('distributor_id', Auth::id())
            ->firstOrFail();

        $block->delete();

        return redirect()->back()->with('success', 'Messages from this retailer have been unblocked.');
    }
}
