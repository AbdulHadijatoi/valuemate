<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    // Register
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'     => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'nullable|string|phone|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed', // use password_confirmation field
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'first_name'     => $request->first_name,
            'last_name'     => $request->last_name,
            'phone'     => $request->phone,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $userRole = Role::firstOrCreate(['name' => 'client']);
        $user->assignRole($userRole);
        
        $token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'status'  => true,
            'message' => 'User registered successfully',
            'data' => [
                'token' => $token,
                'user'  => $user
            ],
        ]);
    }

    // Login
    public function login(Request $request)
    {
        // log request data
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'status' => true,
            'message' => 'User logged in successfully',
            'data' => [
                'user'  => $user,
                'token' => $token,
                'placeholder_image' => Setting::getValue('placeholder-image'),
            ],
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
