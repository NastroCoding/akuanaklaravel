<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isDev
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $check = $request->user()->tokens->first()->name;
        if($check != 'devToken'){
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not a developer.'
            ]);
        };
        
        return $next($request);
    }
}
