<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    public function getOrCreateRoom(Request $request)
    {
        $user = auth()->user();

        $room = ChatRoom::firstOrCreate([
            'user_id' => $user->id
        ]);

        return response()->json($room);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_room_id' => 'required|exists:chat_rooms,id',
            'message' => 'required|string'
        ]);

        $message = ChatMessage::create([
            'chat_room_id' => $request->chat_room_id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return response()->json($message);
    }

    public function getMessages($roomId)
    {
        $user = auth()->user();
        $room = ChatRoom::where('id', $roomId)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                ->orWhere(fn($query) => $user->is_admin); // Admin can see all
            })->firstOrFail();

        return response()->json($room->messages()->with('sender')->get());
    }


}
