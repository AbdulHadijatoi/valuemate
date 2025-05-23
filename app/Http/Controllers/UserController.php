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
    
    public function getData() { 

        $data = User::get();
                    
        return response()->json([
            'status' => true,
            'message' => 'Data retrieved',
            'data' => $data
        ]);
    }

    public function store(Request $r) {
        $r->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'nullable|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed', // use password_confirmation field
        ]);

        $user = User::create([
            'first_name'     => $r->first_name,
            'last_name'     => $r->last_name,
            'phone'     => $r->phone,
            'email'    => $r->email,
            'password' => Hash::make($r->password),
        ]);

        $userRole = Role::firstOrCreate(['name' => 'client']);
        $user->assignRole($userRole);
        
        return response()->json([
            'status' => true,
            'message' => 'Successfully created user',
        ]);
    }

    public function update(Request $r, $user_id) {
        $r->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,'.$user_id,
            'phone'    => 'nullable|string|max:255|unique:users,phone,'.$user_id,
        ]);

        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        $user->update($r->all());

        return response()->json([
            'status' => true,
            'message' => 'Successfully updated user',
        ]);
    }

    public function delete($user_id) {
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Successfully deleted user',
        ]);
    }

    
    public function updatePassword(Request $r, $user_id) {
        $r->validate([
            '' => 'required|string|min:6|confirmed', // use password_confirmation field
        ]);

        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        $user->update(['password' => Hash::make($r->password)]);

        return response()->json([
            'status' => true,
            'message' => 'Successfully updated password',
        ]);
    }

}
