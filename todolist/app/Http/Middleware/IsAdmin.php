<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()){
            return response()->json(['error' => 'Unauthorized'],401);
        }
        
        if (!Auth::check() || !Auth::user()->is_admin) {

            return response()->json(['message' => 'Access Denied'], 403);
        }

        return $next($request);
    }
}
