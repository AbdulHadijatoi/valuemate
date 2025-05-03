<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index() { 
        return Chat::all(); 
    }

    public function store(Request $r) {
        return Chat::create($r->validate(['user_id'=>'required','message'=>'required'])); 
    }

    public function show(Chat $chat) {
        return $chat; 
    }
    
    public function update(Request $request, Chat $chat) {
        $chat->update($request->all()); return $chat; 
    }
    
    public function destroy(Chat $chat) {
        $chat->delete(); return response()->noContent(); 
    }
}
