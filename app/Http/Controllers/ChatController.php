<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index($user_id = null) { 
        if (!$user_id) {
            $user_id = Auth::id();
        } 

        $data = Chat::where('user_id', $user_id)
                    ->get(['message', 'user_id', 'created_at']);
                    
        return response()->json([
            'status' => true,
            'message' => 'Data retrieved',
            'data' => $data
        ]);
    }
    
    public function allData() { 

        $data = Chat::get(['user_id', 'message', 'created_at']);
                    
        return response()->json([
            'status' => true,
            'message' => 'Data retrieved',
            'data' => $data
        ]);
    }

    public function sendUserMessage(Request $r) {
        $r->validate([
            'message' => 'required'
        ]);

        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        Chat::create([
            'user_id' => $user->id,
            'message' => $r->message
        ]); 

        return response()->json([
            'status' => true,
            'message' => 'Success',
        ]);
    }
    
    public function sendAdminMessage(Request $r) {
        $r->validate([
            'message' => 'required'
        ]);

        Chat::create([
            'admin_id' => 1,
            'message' => $r->message
        ]); 

        return response()->json([
            'status' => true,
            'message' => 'Success',
        ]);
    }

}
