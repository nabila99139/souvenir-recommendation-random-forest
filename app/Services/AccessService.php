<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AccessService
{
    /**
     * Check if current user is admin.
     */
    public static function isAdmin(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->isAdmin();
    }

    /**
     * Check if current user is root admin.
     */
    public static function isRootAdmin(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->isRootAdmin();
    }

    /**
     * Check if current user is regular user.
     */
    public static function isRegularUser(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->isRegularUser();
    }

    /**
     * Check if current user has access to admin routes.
     */
    public static function hasAdminAccess(): bool
    {
        return self::isAdmin();
    }

    /**
     * Check if current user has access to root admin routes.
     */
    public static function hasRootAccess(): bool
    {
        return self::isRootAdmin();
    }

    /**
     * Get current user's role.
     */
    public static function getCurrentRole(): string
    {
        if (!Auth::check()) {
            return 'guest';
        }

        return Auth::user()->role ?? 'user';
    }

    /**
     * Get current user's access level.
     */
    public static function getAccessLevel(): int
    {
        if (!Auth::check()) {
            return 0; // guest
        }

        $user = Auth::user();

        if ($user->isRootAdmin()) {
            return 3; // root admin
        }

        if ($user->isAdmin()) {
            return 2; // admin
        }

        return 1; // regular user
    }

    /**
     * Check if user can perform specific action.
     */
    public static function can(string $action): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        $accessLevel = self::getAccessLevel();

        $adminActions = [
            'create.users',
            'edit.users',
            'delete.users',
            'manage.settings',
            'view.all.data',
        ];

        $regularUserActions = [
            'view.catalog',
            'submit.recommendation',
            'view.recommendations',
            'manage.profile',
        ];

        if ($accessLevel >= 2 && in_array($action, $adminActions)) {
            return true;
        }

        if (in_array($action, $regularUserActions)) {
            return true;
        }

        return false;
    }

    /**
     * Check if route is accessible for user.
     */
    public static function isRouteAccessible(string $routeName): bool
    {
        if (!Auth::check()) {
            // Public routes
            $publicRoutes = [
                'welcome',
                'auth.login',
                'auth.register',
                'auth.verify',
            ];
            return in_array($routeName, $publicRoutes);
        }

        $accessLevel = self::getAccessLevel();

        // Root admins have access to all routes
        if ($accessLevel >= 3) {
            return true;
        }

        // Admins have access to most routes
        if ($accessLevel >= 2) {
            $restrictedRoutes = [
                'admin.root.settings',
                // Add other root-only routes
            ];
            return !in_array($routeName, $restrictedRoutes);
        }

        // Regular users have limited access
        $allowedUserRoutes = [
            'home',
            'catalog',
            'recommend.submit',
            'recommend.results',
            'auth.logout',
            // Add other user-accessible routes
        ];

        return in_array($routeName, $allowedUserRoutes);
    }
}
