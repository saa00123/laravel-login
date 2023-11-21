<?php

namespace App\Http\Controllers;

use App\Models\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'nickname' => 'required|unique:login|max:255',
            'password' => 'required|min:6',
            'email' => 'required|email|unique:login|max:255',
            'phone' => 'nullable|max:255'
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = Login::create($validatedData);

        return response()->json(['message' => 'User registered successfully', 'user' => $user]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nickname' => 'required',
            'password' => 'required'
        ]);

        $login = Login::where('nickname', $credentials['nickname'])->first();

        if (!$login || !Hash::check($credentials['password'], $login->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        Session::put('login', $login->id);

        return response()->json(['message' => 'Logged in successfully']);
    }
}
