<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Customer Only Middleware
 * Only allows authenticated Customer users to access the route.
 */
class CustomerOnly
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', 'Please login to continue.');
        }

        $user = Auth::user();

        if (!$user->isCustomer()) {
            return redirect()->route('home')->with('error', 'Access denied. Customer access required.');
        }

        return $next($request);
    }
}