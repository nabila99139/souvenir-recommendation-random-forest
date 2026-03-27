<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Customer Or Admin Only Middleware
 * Allows authenticated Customer users and Root Admin users to access the route.
 * This is useful for routes that should be customer-facing but also accessible to admins for testing/oversight.
 */
class CustomerOrAdminOnly
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                ->with('error', 'Authentication required. Please login to continue.');
        }

        $user = Auth::user();

        // Allow access to customers and admins only
        if (!$user->isCustomer() && !$user->isRoot()) {
            // Redirect to appropriate dashboard based on user role
            if ($user->isSeller()) {
                return redirect()->route('seller.dashboard')
                    ->with('error', 'Access denied. This area is for customers and admins only. You are being redirected to your seller dashboard.');
            }

            return redirect()->route('welcome')
                ->with('error', 'Access denied. Customer or admin access required.');
        }

        return $next($request);
    }
}