<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated AND if they are an admin
        // You would need an 'is_admin' column on your User model, or a role-based system.
        if (Auth::check() && Auth::user()->is_admin) { // Assuming 'is_admin' column exists on User model
            return $next($request);
        }

        // Redirect or abort if not authorized
        return redirect('/dashboard')->with('error', 'You do not have administrative access.');
        // Or abort(403, 'Unauthorized action.');
    }
}
