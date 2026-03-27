@extends('layouts.seller')

@section('title', 'Lead Tracking')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Lead Tracking & Analytics</h2>
        <p class="text-gray-600 mt-1">Track customer interest in your products</p>
    </div>

    <!-- Analytics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Views -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Views</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($leadData['total_views']) }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-eye text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="text-gray-400 text-sm mt-2">
                <i class="fas fa-calendar"></i> All time
            </p>
        </div>

        <!-- Today's Views -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Today's Views</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($leadData['today_views']) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-calendar-day text-green-600 text-xl"></i>
                </div>
            </div>
            <p class="text-gray-400 text-sm mt-2">
                <i class="fas fa-clock"></i> {{ now()->format('M d') }}
            </p>
        </div>

        <!-- This Week -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">This Week</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($leadData['this_week_views']) }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-calendar-week text-purple-600 text-xl"></i>
                </div>
            </div>
            <p class="text-gray-400 text-sm mt-2">
                <i class="fas fa-calendar-alt"></i> {{ now()->startOfWeek()->format('M d') }} - {{ now()->endOfWeek()->format('M d') }}
            </p>
        </div>

        <!-- This Month -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">This Month</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($leadData['this_month_views']) }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-calendar text-orange-600 text-xl"></i>
                </div>
            </div>
            <p class="text-gray-400 text-sm mt-2">
                <i class="fas fa-calendar-check"></i> {{ now()->format('F Y') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Viewed Souvenirs -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-trophy text-yellow-500 mr-2"></i>Top Viewed Souvenirs
            </h3>

            @if(empty($leadData['top_viewed_souvenirs']))
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-box-open text-4xl mb-2"></i>
                    <p>No view data yet</p>
                    <p class="text-sm">Views will appear here when customers browse your souvenirs</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($leadData['top_viewed_souvenirs'] as $index => $souvenir)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="bg-{{ ['yellow', 'gray', 'orange', 'blue', 'green'][$index] }}-100 text-{{ ['yellow', 'gray', 'orange', 'blue', 'green'][$index] }}-600 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $souvenir['name'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $souvenir['category'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-eye text-gray-400"></i>
                                <span class="font-semibold text-gray-700">{{ number_format($souvenir['views']) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Customer Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-history text-blue-500 mr-2"></i>Recent Customer Activity
            </h3>

            @if(empty($leadData['recent_views']))
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p>No recent activity yet</p>
                    <p class="text-sm">Recent views will appear here</p>
                </div>
            @else
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($leadData['recent_views'] as $view)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="bg-blue-100 p-2 rounded-full">
                                    <i class="fas fa-eye text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $view['name'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $view['date'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-chart-line text-gray-400"></i>
                                <span class="font-semibold text-gray-700">{{ number_format($view['views']) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Performance Tips -->
    <div class="mt-8 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg shadow-lg p-6 text-white">
        <h3 class="text-lg font-semibold mb-3 flex items-center">
            <i class="fas fa-lightbulb mr-2"></i>Performance Tips
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-start space-x-3">
                <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                    <i class="fas fa-camera"></i>
                </div>
                <div>
                    <p class="font-semibold mb-1">High-Quality Images</p>
                    <p class="text-sm text-emerald-100">Use clear, well-lit photos to showcase your products</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                    <i class="fas fa-pen"></i>
                </div>
                <div>
                    <p class="font-semibold mb-1">Detailed Descriptions</p>
                    <p class="text-sm text-emerald-100">Include materials, dimensions, and craftsmanship details</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                    <i class="fas fa-tags"></i>
                </div>
                <div>
                    <p class="font-semibold mb-1">Appropriate Pricing</p>
                    <p class="text-sm text-emerald-100">Set competitive prices that reflect quality</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection