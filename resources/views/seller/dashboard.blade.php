@extends('layouts.seller')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Welcome Message -->
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">
            Welcome back, {{ $user->name }}!
        </h2>
        <p class="text-emerald-100">
            @if($user->hasBusinessProfile())
                Manage your {{ $user->souvenir_count }} souvenirs and track customer interest.
            @else
                Complete your business profile to start selling souvenirs!
            @endif
        </p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Souvenirs -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Souvenirs</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_souvenirs'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="text-gray-400 text-sm mt-2">
                <i class="fas fa-info-circle"></i> Products in your catalog
            </p>
        </div>

        <!-- Total Views -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Views</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_views']) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-eye text-green-600 text-xl"></i>
                </div>
            </div>
            <p class="text-gray-400 text-sm mt-2">
                <i class="fas fa-chart-line"></i> Customer engagement
            </p>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Recent Views</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ count($stats['recent_views']) }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-clock text-purple-600 text-xl"></i>
                </div>
            </div>
            <p class="text-gray-400 text-sm mt-2">
                <i class="fas fa-history"></i> Recent customer activity
            </p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-plus-circle text-emerald-600 mr-2"></i>Quick Actions
            </h3>
            <div class="space-y-3">
                <a href="{{ route('seller.souvenirs') }}" class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition">
                    <span class="text-gray-700">
                        <i class="fas fa-box text-emerald-600 mr-2"></i>Manage Souvenirs
                    </span>
                    <i class="fas fa-arrow-right text-emerald-600"></i>
                </a>
                <a href="{{ route('seller.create-souvenir') }}" class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition">
                    <span class="text-gray-700">
                        <i class="fas fa-plus text-emerald-600 mr-2"></i>Add New Souvenir
                    </span>
                    <i class="fas fa-arrow-right text-emerald-600"></i>
                </a>
                <a href="{{ route('seller.business-profile') }}" class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition">
                    <span class="text-gray-700">
                        <i class="fas fa-building text-emerald-600 mr-2"></i>Update Business Profile
                    </span>
                    <i class="fas fa-arrow-right text-emerald-600"></i>
                </a>
                <a href="{{ route('seller.leads') }}" class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition">
                    <span class="text-gray-700">
                        <i class="fas fa-chart-bar text-emerald-600 mr-2"></i>View Analytics
                    </span>
                    <i class="fas fa-arrow-right text-emerald-600"></i>
                </a>
            </div>
        </div>

        <!-- Recent Views Table -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-eye text-purple-600 mr-2"></i>Recent Customer Views
            </h3>
            @if(empty($stats['recent_views']))
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p>No recent views yet</p>
                    <p class="text-sm">Views will appear here when customers browse your souvenirs</p>
                </div>
            @else
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($stats['recent_views'] as $view)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $view['name'] }}</p>
                                <p class="text-sm text-gray-500">{{ $view['date'] }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-eye text-gray-400"></i>
                                <span class="font-semibold text-gray-700">{{ number_format($view['views']) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Business Profile Status -->
    @if(!$user->hasBusinessProfile())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
            <div class="flex items-start">
                <div class="bg-yellow-100 p-2 rounded-full mr-4">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-yellow-800 font-semibold mb-1">Complete Your Business Profile</h4>
                    <p class="text-yellow-700 text-sm mb-3">
                        To maximize your sales and customer trust, please complete your business profile information including business name, address, and contact details.
                    </p>
                    <a href="{{ route('seller.business-profile') }}" class="inline-block bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition text-sm">
                        <i class="fas fa-edit mr-1"></i> Complete Profile
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection