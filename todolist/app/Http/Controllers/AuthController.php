<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Events\UserOnlineStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);        
    
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),

        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'user' => $user], 201);
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
    
            $user = Auth::user();
            $user->is_online = true; 
            $user->save();
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json(['access_token' => $token, 'user' => $user]);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        Log::info('Logout request received with token: ' . $request->bearerToken());
    
        try {
            $user = $request->user();
    
            if ($user === null) {
                Log::error('Logout attempt without authenticated user.');
                return response()->json(['error' => 'No authenticated user found.'], 401);
            }
    
            Log::info('Authenticated user ID: ' . $user->id);
    
            $user->update(['is_online' => false]);
    
            $user->tokens->each(function ($token) {
                $token->delete();
            });
    
            return response()->json(['message' => 'Successfully logged out']);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    
}
