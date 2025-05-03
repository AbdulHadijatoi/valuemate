<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index() { 
        return Notification::all(); 
    }
    
    public function store(Request $r) {
        return Notification::create($r->validate([
            'user_id'=>'required',
            'title'=>'required',
            'message'=>'required',
            'type'=>'required'
        ]));
    }
    
    public function show(Notification $notification) { 
        return $notification; 
    }
    
    public function update(Request $r, Notification $notification) { 
        $notification->update($r->all()); return $notification; 
    }
    
    public function destroy(Notification $notification) { 
        $notification->delete(); return response()->noContent(); 
    }
    
}
