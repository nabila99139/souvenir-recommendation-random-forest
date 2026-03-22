<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AccessControl
{
    /**
     * Validates user permissions based on route names.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $this->denyAccess($request, 'Authentication required.');
        }

        $user = Auth::user();

        // Root admins have access to all routes
        if ($user->isRootAdmin()) {
            return $next($request);
        }

        // Check if user has permission for this route
        $routeName = Route::current()->getName();

        if (!$this->hasPermission($user, $routeName)) {
            return $this->denyAccess($request, 'Access denied. Insufficient permissions.');
        }

        return $next($request);
    }

    /**
     * Check if user has permission for the route.
     */
    private function hasPermission($user, string $routeName): bool
    {
        // Admin users have access to all public routes
        if ($user->isAdmin()) {
            $restrictedRoutes = [
                'admin.settings',
                'admin.users.create',
                'admin.users.delete',
                // Add other sensitive admin routes
            ];

            return !in_array($routeName, $restrictedRoutes);
        }

        // Regular users have limited access
        $allowedRoutes = [
            'home',
            'catalog',
            'recommend.submit',
            'recommend.results',
            'auth.logout',
            // Add other user-accessible routes
        ];

        return in_array($routeName, $allowedRoutes);
    }

    /**
     * Handle access denial.
     */
    private function denyAccess(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'statusCode' => 403
            ], 403);
        }

        return redirect()->back()->with('error', $message);
    }
}
