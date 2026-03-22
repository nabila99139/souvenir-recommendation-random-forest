<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemSettingsController extends Controller
{
    /**
     * Display system settings page.
     */
    public function index(): View
    {
        $settings = $this->getSystemSettings();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update system settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'maintenance_mode' => 'boolean',
            'enable_registration' => 'boolean',
            'enable_recommendations' => 'boolean',
        ]);

        // Store settings in session or database
        // For now, we'll use session storage
        session([
            'system_settings' => $validated
        ]);

        return back()->with('success', 'System settings updated successfully.');
    }

    /**
     * Display system information page.
     */
    public function info(): View
    {
        $systemInfo = [
            'laravel_version' => app()->version(),
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_connection' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
        ];

        return view('admin.settings.info', compact('systemInfo'));
    }

    /**
     * Get system settings.
     */
    private function getSystemSettings(): array
    {
        return [
            'site_name' => config('app.name', 'Souvenir Recommendation System'),
            'site_description' => 'AI-powered souvenir recommendation system',
            'maintenance_mode' => false,
            'enable_registration' => true,
            'enable_recommendations' => true,
        ];
    }
}
