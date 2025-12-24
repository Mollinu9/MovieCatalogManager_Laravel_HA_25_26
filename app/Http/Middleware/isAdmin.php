<?php

namespace App\Http\Middleware;

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
        // Check if they are logged in
        if (!auth()->check())
        {
            abort(403, 'You are not logged in.');
        }

        // Check if they are an admin
        if (!auth()->user()->is_admin) 
        {
            abort(403, 'You are logged in, but you are not an admin.');
        }

        // if they are move on
        return $next($request);
    }
}
