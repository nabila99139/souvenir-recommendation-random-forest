<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Root Only Middleware
 * Only allows authenticated Root users to access the route.
 */
class RootOnly
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

        if (!$user->isRoot()) {
            // Redirect to appropriate dashboard based on user role
            if ($user->isCustomer()) {
                return redirect()->route('home')
                    ->with('error', 'Access denied. This area is for administrators only. You are being redirected to the customer area.');
            } elseif ($user->isSeller()) {
                return redirect()->route('seller.dashboard')
                    ->with('error', 'Access denied. This area is for administrators only. You are being redirected to your seller dashboard.');
            }

            return redirect()->route('welcome')
                ->with('error', 'Access denied. Root administrator access required.');
        }

        return $next($request);
    }
}