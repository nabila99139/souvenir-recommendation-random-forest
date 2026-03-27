<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Souvenir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
     * Show create admin user form (Root only).
     */
    public function createUser(): View
    {
        return view('admin.create-user');
    }

    /**
     * Create new admin user (Root only).
     */
    public function storeUser(Request $request)
    {
        // Check if current user is Root admin
        $currentUser = Auth::user();
        if (!$currentUser->isRoot()) {
            return back()->with('error', 'Only Root admins can create other Root accounts.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|max:50|confirmed',
            'role' => 'required|in:root,customer,seller',
        ], [
            'name.required' => 'Name is required',
            'name.max' => 'Name must not exceed 255 characters',
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'email.max' => 'Email address must not exceed 255 characters',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password.max' => 'Password must not exceed 50 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'role.required' => 'Role is required',
            'role.in' => 'Invalid role selected',
        ]);

        try {
            // Determine admin status based on role
            $isAdmin = ($request->role === User::ROLE_ROOT);

            // Create user
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'is_admin' => $isAdmin,
                'authorized_by' => $currentUser->id,
            ]);

            Log::info('New user created by Root admin', [
                'admin_id' => $currentUser->id,
                'new_user_email' => $request->email,
                'new_user_role' => $request->role,
                'timestamp' => now()
            ]);

            return redirect()->route('admin.users')->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to create user', [
                'admin_id' => $currentUser->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to create user. Please try again.']);
        }
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
