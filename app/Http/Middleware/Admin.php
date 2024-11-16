<?php

namespace App\Http\Middleware;

use Closure;
use app\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !(Auth::user()->role === 0)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $next($request); //folytatódhat a kérés
    }

}
