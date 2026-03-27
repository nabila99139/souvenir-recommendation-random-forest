<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Seller Only Middleware
 * Only allows authenticated Seller users to access the route.
 */
class SellerOnly
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

        if (!$user->isSeller()) {
            // Redirect to appropriate dashboard based on user role
            if ($user->isCustomer()) {
                return redirect()->route('home')
                    ->with('error', 'Access denied. This area is for sellers only. You are being redirected to the customer area.');
            } elseif ($user->isRoot()) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Access denied. This area is for sellers only. You are being redirected to your admin dashboard.');
            }

            return redirect()->route('welcome')
                ->with('error', 'Access denied. Seller access required.');
        }

        return $next($request);
    }
}