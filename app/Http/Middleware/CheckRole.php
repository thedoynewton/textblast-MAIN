<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Check if the authenticated user's role matches the required role
            if (Auth::user()->role === $role) {
                return $next($request);
            }

            // If the user's role does not match, redirect them with an error message
            return redirect('/')->with('error', 'You do not have access to this page.');
        }

        // If the user is not authenticated, redirect them to the login page
        return redirect()->route('login')->with('error', 'Please log in to access this page.');
    }
}
