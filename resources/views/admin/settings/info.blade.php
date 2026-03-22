@extends('layouts.admin')

@section('title', 'System Information')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">
            <i class="fas fa-server text-indigo-600 mr-3"></i>System Information
        </h2>
        <p class="text-gray-600">View detailed information about your system environment.</p>
    </div>

    <!-- System Information Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">
                <i class="fas fa-code text-indigo-600 mr-2"></i>Environment
            </h3>

            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Laravel Version</span>
                    <span class="font-semibold text-gray-900">{{ $systemInfo['laravel_version'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">PHP Version</span>
                    <span class="font-semibold text-gray-900">{{ $systemInfo['php_version'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Server Software</span>
                    <span class="font-semibold text-gray-900">{{ $systemInfo['server_software'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">
                <i class="fas fa-database text-indigo-600 mr-2"></i>Configuration
            </h3>

            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Database Connection</span>
                    <span class="font-semibold text-gray-900">{{ $systemInfo['database_connection'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Cache Driver</span>
                    <span class="font-semibold text-gray-900">{{ $systemInfo['cache_driver'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Session Driver</span>
                    <span class="font-semibold text-gray-900">{{ $systemInfo['session_driver'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">
                <i class="fas fa-user-shield text-indigo-600 mr-2"></i>Access Control
            </h3>

            <div class="space-y-4">
                <div class="p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <span class="font-semibold text-green-800">Root Admin Access</span>
                    </div>
                    <p class="text-sm text-green-700 mt-1">Full system access granted</p>
                </div>
                <div class="p-4 bg-indigo-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-indigo-600 mr-2"></i>
                        <span class="font-semibold text-indigo-800">Authentication System</span>
                    </div>
                    <p class="text-sm text-indigo-700 mt-1">OTP-based verification active</p>
                </div>
                <div class="p-4 bg-purple-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-purple-600 mr-2"></i>
                        <span class="font-semibold text-purple-800">Middleware Protection</span>
                    </div>
                    <p class="text-sm text-purple-700 mt-1">All admin routes protected</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">
                <i class="fas fa-chart-line text-indigo-600 mr-2"></i>Quick Actions
            </h3>

            <div class="space-y-3">
                <a href="{{ route('admin.users.index') }}" class="block p-3 bg-gray-50 rounded-lg hover:bg-indigo-50 transition">
                    <div class="flex items-center">
                        <i class="fas fa-users text-indigo-600 mr-3"></i>
                        <span class="font-semibold text-gray-900">Manage Users</span>
                    </div>
                </a>
                <a href="{{ route('admin.souvenirs.index') }}" class="block p-3 bg-gray-50 rounded-lg hover:bg-indigo-50 transition">
                    <div class="flex items-center">
                        <i class="fas fa-gift text-indigo-600 mr-3"></i>
                        <span class="font-semibold text-gray-900">Manage Souvenirs</span>
                    </div>
                </a>
                <a href="{{ route('admin.settings.general') }}" class="block p-3 bg-gray-50 rounded-lg hover:bg-indigo-50 transition">
                    <div class="flex items-center">
                        <i class="fas fa-cog text-indigo-600 mr-3"></i>
                        <span class="font-semibold text-gray-900">System Settings</span>
                    </div>
                </a>
                <a href="{{ route('home') }}" class="block p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                    <div class="flex items-center">
                        <i class="fas fa-home text-green-600 mr-3"></i>
                        <span class="font-semibold text-green-900">Go to Application</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
