<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    /**
     * Restricts access to admin users only.
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

        if (!$user->isAdmin()) {
            return $this->denyAccess($request, 'Access denied. Admin privileges required.');
        }

        return $next($request);
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
