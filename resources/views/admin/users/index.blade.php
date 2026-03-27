@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">
            <i class="fas fa-users text-indigo-600 mr-3"></i>User Management
        </h2>
        <div class="flex space-x-2">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-2"></i>Add User
            </button>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Search Users</label>
                <input type="text" placeholder="Search by name or email..."
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Role Filter</label>
                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Status</label>
                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-indigo-100 p-2 rounded-full mr-3">
                                    <i class="fas fa-user text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-600">ID: {{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-gray-900">{{ $user->email }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->is_admin)
                                <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full">
                                    Admin
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2 py-1 rounded-full">
                                    User
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded-full">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->is_admin)
                                    <form action="{{ route('admin.users.demote', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-user-slash" title="Demote to User"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.promote-root', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-purple-600 hover:text-purple-900">
                                            <i class="fas fa-user-shield" title="Promote to Admin"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="text-center">
                                <i class="fas fa-users text-6xl text-gray-400 mb-4"></i>
                                <h3 class="text-xl font-bold text-gray-700 mb-2">No Users Found</h3>
                                <p class="text-gray-500">Get started by creating your first user.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
