<?php

namespace App\Http\Middleware;

use App\Models\Administrator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $check = $request->user()->tokens->first()->name;
        if($check != 'adminToken'){
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ]);
        };
        
        return $next($request);
    }
}
