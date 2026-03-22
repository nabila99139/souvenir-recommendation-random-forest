<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Souvenir;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function dashboard(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('is_admin', true)->count(),
            'total_souvenirs' => Souvenir::count(),
            'total_recommendations' => \App\Models\Recommendation::count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentSouvenirs = Souvenir::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentSouvenirs'));
    }

    /**
     * Display admin settings page.
     */
    public function settings(): View
    {
        return view('admin.settings');
    }

    /**
     * Display admin users management page.
     */
    public function users(): View
    {
        $users = User::latest()->paginate(10);
        return view('admin.users', compact('users'));
    }

    /**
     * Display admin souvenirs management page.
     */
    public function souvenirs(): View
    {
        $souvenirs = Souvenir::latest()->paginate(10);
        return view('admin.souvenirs', compact('souvenirs'));
    }

    /**
     * Display system overview.
     */
    public function system(): View
    {
        return view('admin.system');
    }
}
