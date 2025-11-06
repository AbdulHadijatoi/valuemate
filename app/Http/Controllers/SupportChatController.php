<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use App\Traits\Cacheable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportChatController extends Controller
{
    use Cacheable;

    /**
     * User: Get or create their support chat room
     */
    public function getOrCreateRoom(Request $request)
    {
        $user = auth()->user();

        $room = ChatRoom::firstOrCreate(
            ['user_id' => $user->id],
            ['user_id' => $user->id]
        );

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $room->id,
                'user_id' => $room->user_id,
                'created_at' => $room->created_at,
            ]
        ]);
    }

    /**
     * User: Get their messages
     */
    public function getUserMessages(Request $request)
    {
        $user = auth()->user();
        
        $cacheKey = 'user_chat_messages_' . $user->id;
        
        return $this->remember($cacheKey, function () use ($user) {
            $room = ChatRoom::where('user_id', $user->id)->first();
            
            if (!$room) {
                return response()->json([
                    'status' => true,
                    'data' => []
                ]);
            }

            $messages = ChatMessage::where('chat_room_id', $room->id)
                ->with(['sender:id,first_name,last_name,email'])
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) use ($user) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'sender_id' => $message->sender_id,
                        'sender_name' => $message->sender ? $message->sender->first_name . ' ' . $message->sender->last_name : 'System',
                        'is_admin' => $message->sender_id !== $user->id,
                        'is_read' => $message->is_read,
                        'created_at' => $message->created_at ? Carbon::parse($message->created_at)->format('Y-m-d H:i:s') : null,
                        'created_at_human' => $message->created_at ? Carbon::parse($message->created_at)->diffForHumans() : null,
                    ];
                });

            return response()->json([
                'status' => true,
                'data' => $messages
            ]);
        }, 300); // Cache for 5 minutes (shorter TTL for real-time feel)
    }

    /**
     * User: Send a message
     */
    public function sendUserMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000'
        ]);

        $user = auth()->user();
        
        $room = ChatRoom::firstOrCreate(
            ['user_id' => $user->id],
            ['user_id' => $user->id]
        );

        $message = ChatMessage::create([
            'chat_room_id' => $room->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'is_read' => false, // Admin needs to read it
        ]);

        // Clear cache for this user and admin
        $this->clearCache('user_chat_messages_' . $user->id);
        $this->clearCache('admin_chat_rooms');
        $this->clearCache('admin_unread_count');
        $this->clearCache('admin_room_messages_' . $room->id);

        return response()->json([
            'status' => true,
            'message' => 'Message sent successfully',
            'data' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'created_at' => $message->created_at ? Carbon::parse($message->created_at)->format('Y-m-d H:i:s') : null,
            ]
        ]);
    }

    /**
     * Admin: Get all chat rooms with unread count
     */
    public function getAdminChatRooms(Request $request)
    {
        return $this->remember('admin_chat_rooms', function () {
            $rooms = ChatRoom::with(['user:id,first_name,last_name,email', 'messages'])
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($room) {
                    // Count unread messages from the user (not from admin)
                    $unreadCount = ChatMessage::where('chat_room_id', $room->id)
                        ->where('sender_id', $room->user_id)
                        ->where('is_read', false)
                        ->count();
                    
                    $lastMessage = $room->messages()->latest()->first();
                    
                    return [
                        'id' => $room->id,
                        'user_id' => $room->user_id,
                        'user_name' => $room->user ? $room->user->first_name . ' ' . $room->user->last_name : 'Unknown',
                        'user_email' => $room->user ? $room->user->email : '',
                        'unread_count' => $unreadCount,
                        'last_message' => $lastMessage ? [
                            'message' => $lastMessage->message,
                            'created_at' => $lastMessage->created_at ? Carbon::parse($lastMessage->created_at)->format('Y-m-d H:i:s') : null,
                            'created_at_human' => $lastMessage->created_at ? Carbon::parse($lastMessage->created_at)->diffForHumans() : null,
                        ] : null,
                        'updated_at' => $room->updated_at ? Carbon::parse($room->updated_at)->format('Y-m-d H:i:s') : null,
                        'created_at' => $room->created_at ? Carbon::parse($room->created_at)->format('Y-m-d H:i:s') : null,
                    ];
                });

            $totalUnread = $rooms->sum('unread_count');

            return response()->json([
                'status' => true,
                'data' => $rooms,
                'total_unread' => $totalUnread
            ]);
        }, 300); // Cache for 5 minutes
    }

    /**
     * Admin: Get messages for a specific room
     */
    public function getAdminRoomMessages($roomId)
    {
        // Clear cache first since we're about to mark messages as read
        $this->clearCache('admin_room_messages_' . $roomId);
        
        $room = ChatRoom::with(['user:id,first_name,last_name,email'])
            ->findOrFail($roomId);

        $messages = ChatMessage::where('chat_room_id', $roomId)
            ->with(['sender:id,first_name,last_name,email'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) use ($room) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender ? $message->sender->first_name . ' ' . $message->sender->last_name : 'System',
                    'is_admin' => $message->sender_id !== $room->user_id, // Admin is not the room owner
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at ? Carbon::parse($message->created_at)->format('Y-m-d H:i:s') : null,
                    'created_at_human' => $message->created_at ? Carbon::parse($message->created_at)->diffForHumans() : null,
                ];
            });

        // Mark all user messages as read when admin views the room
        ChatMessage::where('chat_room_id', $roomId)
            ->where('sender_id', $room->user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Clear all related caches after marking as read
        $this->clearCache('admin_chat_rooms');
        $this->clearCache('admin_unread_count');
        $this->clearCache('user_chat_messages_' . $room->user_id);

        return response()->json([
            'status' => true,
            'data' => [
                'room' => [
                    'id' => $room->id,
                    'user_id' => $room->user_id,
                    'user_name' => $room->user ? $room->user->first_name . ' ' . $room->user->last_name : 'Unknown',
                    'user_email' => $room->user ? $room->user->email : '',
                ],
                'messages' => $messages
            ]
        ]);
    }

    /**
     * Admin: Send reply to user
     */
    public function sendAdminReply(Request $request, $roomId)
    {
        $request->validate([
            'message' => 'required|string|max:5000'
        ]);

        $room = ChatRoom::findOrFail($roomId);
        $admin = auth()->user();

        $message = ChatMessage::create([
            'chat_room_id' => $roomId,
            'sender_id' => $admin->id,
            'message' => $request->message,
            'is_read' => true, // Admin messages are auto-read
        ]);

        // Clear cache for this room and user
        $this->clearCache('admin_chat_rooms');
        $this->clearCache('admin_unread_count');
        $this->clearCache('admin_room_messages_' . $roomId);
        $this->clearCache('user_chat_messages_' . $room->user_id);

        return response()->json([
            'status' => true,
            'message' => 'Reply sent successfully',
            'data' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'created_at' => $message->created_at ? Carbon::parse($message->created_at)->format('Y-m-d H:i:s') : null,
            ]
        ]);
    }

    /**
     * Admin: Get unread count (for badge)
     */
    public function getUnreadCount()
    {
        return $this->remember('admin_unread_count', function () {
            // Get all chat rooms
            $rooms = ChatRoom::with('user')->get();
            
            $unreadCount = 0;
            foreach ($rooms as $room) {
                // Count unread messages from the user (not from admin)
                $count = ChatMessage::where('chat_room_id', $room->id)
                    ->where('sender_id', $room->user_id)
                    ->where('is_read', false)
                    ->count();
                $unreadCount += $count;
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'unread_count' => $unreadCount
                ]
            ]);
        }, 300); // Cache for 5 minutes
    }
}

