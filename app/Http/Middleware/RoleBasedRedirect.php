<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role Based Redirect Middleware
 * Redirects users to their appropriate dashboard based on role.
 */
class RoleBasedRedirect
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $routeName = $request->route()->getName();

        // Define protected routes and their allowed roles
        $protectedRoutes = [
            'home' => [User::ROLE_CUSTOMER],
            'catalog' => [User::ROLE_CUSTOMER],
            'recommend.submit' => [User::ROLE_CUSTOMER],
            'recommend.results' => [User::ROLE_CUSTOMER],
        ];

        // Redirect users to their dashboard if accessing wrong routes
        foreach ($protectedRoutes as $route => $allowedRoles) {
            if ($routeName === $route && !in_array($user->role, $allowedRoles)) {
                return redirect()->route($user->getDashboardRoute());
            }
        }

        return $next($request);
    }
}