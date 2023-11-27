<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all(['id', 'name']);
        return response()->json($users);
    }

    public function toggleCrudPermission(Request $request, User $user)
    {
        $user->save();

        return response()->json(['success' => true]);
    }

    public function updateCrudPermission(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        $user->fill($request->only(['create_allowed', 'update_allowed', 'delete_allowed']));
        $user->save();
    
        return response()->json($user);
    }
}
