<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Events\UserOnlineStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * 로그인 메소드
     * @param Request $request 요청 데이터
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    
        $user = Auth::user();
        $user->is_online = true; 
        $user->save();
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json(['access_token' => $token, 'user' => $user]);
    }

    /**
     * 로그아웃 메소드
     * @param Request $request 요청 데이터
     * @return \Illuminate\Http\JsonResponse
     */
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
