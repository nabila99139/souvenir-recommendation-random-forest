<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Display all users.
     */
    public function index(): View
    {
        $users = User::latest()->paginate(10);
        $currentUser = Auth::user();
        return view('admin.users.index', compact('users', 'currentUser'));
    }

    /**
     * Show user details.
     */
    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show user edit form.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user information.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $currentUser = Auth::user();

        // Only Root admins can change user roles to root
        if ($request->role === User::ROLE_ROOT && !$currentUser->isRoot()) {
            return back()->with('error', 'Only Root admins can assign Root role.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:root,customer,seller,admin',
        ]);

        // Update user with new role
        $isAdmin = ($validated['role'] === User::ROLE_ROOT || $validated['role'] === 'admin');
        $validated['is_admin'] = $isAdmin;
        unset($validated['role']); // Don't update role column if using legacy admin

        // Use role from request if not using legacy admin
        if ($request->role !== 'admin') {
            $user->role = $request->role;
        }

        $user->update($validated);
        $user->is_admin = $isAdmin; // Update is_admin based on role
        $user->save();

        Log::info('User role updated by admin', [
            'admin_id' => $currentUser->id,
            'user_id' => $user->id,
            'old_role' => $user->getOriginal('role'),
            'new_role' => $request->role,
            'timestamp' => now()
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Delete user.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting the current user
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Promote user to Root (Admin) role.
     */
    public function promoteToAdmin(User $user): RedirectResponse
    {
        $currentUser = Auth::user();

        if (!$currentUser->isRoot()) {
            return back()->with('error', 'Only Root admins can promote users to Root role.');
        }

        $user->makeRoot();

        Log::info('User promoted to Root by admin', [
            'admin_id' => $currentUser->id,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'timestamp' => now()
        ]);

        return back()->with('success', 'User promoted to Root admin successfully.');
    }

    /**
     * Change user role to Customer.
     */
    public function changeToCustomer(User $user): RedirectResponse
    {
        $user->makeCustomer();

        Log::info('User role changed to Customer', [
            'admin_id' => Auth::id(),
            'user_id' => $user->id,
            'user_email' => $user->email,
            'timestamp' => now()
        ]);

        return back()->with('success', 'User role changed to Customer successfully.');
    }

    /**
     * Change user role to Seller.
     */
    public function changeToSeller(User $user): RedirectResponse
    {
        $user->makeSeller();

        Log::info('User role changed to Seller', [
            'admin_id' => Auth::id(),
            'user_id' => $user->id,
            'user_email' => $user->email,
            'timestamp' => now()
        ]);

        return back()->with('success', 'User role changed to Seller successfully.');
    }

    /**
     * Demote user from Root (Admin) role to Customer.
     */
    public function demoteFromAdmin(User $user): RedirectResponse
    {
        $currentUser = Auth::user();

        if (!$currentUser->isRoot()) {
            return back()->with('error', 'Only Root admins can demote users.');
        }

        $user->makeCustomer();

        Log::info('User demoted from Root by admin', [
            'admin_id' => $currentUser->id,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'timestamp' => now()
        ]);

        return back()->with('success', 'User demoted to Customer successfully.');
    }
}
