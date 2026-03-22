@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Dashboard Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-shield-alt text-indigo-600 mr-3"></i>
            Admin Dashboard
        </h1>
        <p class="text-gray-600">Welcome back, Root Admin! Manage your souvenir recommendation system.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">Total Users</p>
                    <p class="text-3xl font-bold text-indigo-600">{{ $stats['total_users'] }}</p>
                </div>
                <div class="bg-indigo-100 p-3 rounded-full">
                    <i class="fas fa-users text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">Total Admins</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['total_admins'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-user-shield text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">Total Souvenirs</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_souvenirs'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-gift text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">Recommendations</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $stats['total_recommendations'] }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-magic text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>Quick Actions
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                <i class="fas fa-users text-indigo-600 text-2xl mb-2"></i>
                <span class="font-semibold text-indigo-800">Manage Users</span>
            </a>
            <a href="{{ route('admin.souvenirs.index') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                <i class="fas fa-gift text-purple-600 text-2xl mb-2"></i>
                <span class="font-semibold text-purple-800">Manage Souvenirs</span>
            </a>
            <a href="{{ route('admin.settings.general') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                <i class="fas fa-cog text-green-600 text-2xl mb-2"></i>
                <span class="font-semibold text-green-800">System Settings</span>
            </a>
            <a href="{{ route('admin.settings.info') }}" class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                <i class="fas fa-info-circle text-orange-600 text-2xl mb-2"></i>
                <span class="font-semibold text-orange-800">System Info</span>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Users -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-user-plus text-blue-500 mr-2"></i>Recent Users
            </h2>
            <div class="space-y-4">
                @forelse($recentUsers as $user)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-indigo-100 p-2 rounded-full mr-3">
                                <i class="fas fa-user text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div>
                            @if($user->is_admin)
                                <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full">
                                    Admin
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2 py-1 rounded-full">
                                    User
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No recent users found.</p>
                @endforelse
            </div>
            <a href="{{ route('admin.users.index') }}" class="block text-center mt-4 text-indigo-600 font-semibold hover:text-indigo-800">
                View All Users <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <!-- Recent Souvenirs -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-gift text-pink-500 mr-2"></i>Recent Souvenirs
            </h2>
            <div class="space-y-4">
                @forelse($recentSouvenirs as $souvenir)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-2 rounded-full mr-3">
                                <i class="fas fa-gift text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $souvenir->name }}</p>
                                <p class="text-sm text-gray-600">{{ ucfirst($souvenir->category) }}</p>
                            </div>
                        </div>
                        <div>
                            <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-1 rounded-full">
                                {{ ucfirst($souvenir->price_range) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No recent souvenirs found.</p>
                @endforelse
            </div>
            <a href="{{ route('admin.souvenirs.index') }}" class="block text-center mt-4 text-indigo-600 font-semibold hover:text-indigo-800">
                View All Souvenirs <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</div>
@endsection
