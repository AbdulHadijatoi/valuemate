<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index($user_id = null) { 
        if (!$user_id) {
            $user_id = Auth::id();
        } 

        $data = User::where('id', $user_id)
                    ->get();
                    
        return response()->json([
            'status' => true,
            'message' => 'Data retrieved',
            'data' => $data
        ]);
    }
    
    public function allData() { 

        $data = User::get();
                    
        return response()->json([
            'status' => true,
            'message' => 'Data retrieved',
            'data' => $data
        ]);
    }

    public function createUser(Request $r) {
        $r->validate([
            'message' => 'required'
        ]);

        $user = User::create([
            'name'     => $r->name,
            'email'    => $r->email,
            'password' => Hash::make($r->password),
        ]);

        $userRole = Role::firstOrCreate(['name' => 'client']);
        $user->assignRole($userRole);
        
        return response()->json([
            'status' => true,
            'message' => 'Success',
        ]);
    }

}
