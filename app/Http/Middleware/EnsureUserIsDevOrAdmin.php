<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsDevOrAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $isDev = str_contains($user->username, 'dev'); 
        $isAdmin = $user->administrator && str_contains($user->administrator->username, 'admin'); 

        if (!$isDev && !$isAdmin) {
            return response()->json(['message' => 'Anda bukan Developer atau admin'], 403);
        }

        return $next($request);
    }
}
