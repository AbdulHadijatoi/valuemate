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

        
        return response()->json([
            'status' => true,
            'message' => 'Data retrieved',
            'data' => $data
        ]);
    }
    
    public function getData() { 

        $data = Chat::get(['user_id', 'message','status', 'created_at']);
        
        $grouped = $data->groupBy(function ($item) {
            return $item->propertyType->name ?? 'Unknown'; // Group by property type name
        })->map(function ($items, $propertyTypeName) {
            return [
                'property_type' => $propertyTypeName,
                'property_type_id' => $items->first()->property_type_id,
                'services' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'service_type_id' => $item->service_type_id,
                        'service_type_name' => $item->serviceType->name ?? null,
                        'created_at_date' => $item->serviceType && $item->serviceType->created_at ? Carbon::parse($item->serviceType->created_at)->format("Y-m-d") : null,
                        'created_at_time' => $item->serviceType && $item->serviceType->created_at ? Carbon::parse($item->serviceType->created_at)->format("H:i:s") : null,
                    ];
                })->values(),
            ];
        })->values();

        
        
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
            'message' => 'Successfully sent message',
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
