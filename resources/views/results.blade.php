@extends('layouts.app')

@section('title', 'Recommendation Results')

@push('styles')
<style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Results Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            <i class="fas fa-magic text-indigo-600"></i>
            Your Personalized Recommendations
        </h1>
        <div class="max-w-2xl mx-auto">
            <p class="text-xl text-gray-600 mb-4">
                Based on your preferences, we found <span class="font-bold text-indigo-600">{{ $total }}</span> perfect souvenirs!
            </p>
            <div class="bg-indigo-50 rounded-lg p-4 inline-block">
                <p class="text-gray-700">
                    <i class="fas fa-tag text-indigo-600 mr-2"></i>
                    <span class="font-semibold">Predicted Category:</span>
                    <span class="font-bold text-indigo-600 ml-2">{{ ucfirst($predictedCategory) }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Souvenir Cards -->
    @if($recommendations->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @foreach($recommendations as $souvenir)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:scale-105">
                    <!-- Image Placeholder -->
                    <div class="bg-gradient-to-br from-indigo-100 to-purple-100 h-48 flex items-center justify-center">
                        @if($souvenir->image_path)
                            <img src="{{ asset($souvenir->image_path) }}" alt="{{ $souvenir->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="text-center">
                                <i class="fas fa-gift text-6xl text-indigo-400"></i>
                                <p class="text-indigo-400 mt-2 text-sm">Image not available</p>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-lg text-gray-900">{{ $souvenir->name }}</h3>
                            @if($souvenir->price_range === 'low')
                                <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full">
                                    <i class="fas fa-dollar-sign"></i> Affordable
                                </span>
                            @elseif($souvenir->price_range === 'medium')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full">
                                    <i class="fas fa-dollar-sign"></i> Moderate
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded-full">
                                    <i class="fas fa-dollar-sign"></i> Premium
                                </span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <span class="inline-block bg-indigo-100 text-indigo-800 text-sm px-3 py-1 rounded-full">
                                <i class="fas fa-tag mr-1"></i>{{ ucfirst($souvenir->category) }}
                            </span>
                        </div>

                        @if($souvenir->description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ $souvenir->description }}
                            </p>
                        @endif

                        <div class="flex space-x-2">
                            <button class="flex-1 bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-indigo-700 transition">
                                <i class="fas fa-info-circle mr-2"></i>Details
                            </button>
                            <button class="flex-1 bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-shopping-cart mr-2"></i>Add
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-search text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-2xl font-bold text-gray-700 mb-2">No souvenirs found</h3>
            <p class="text-gray-500">We couldn't find souvenirs matching your preferences. Try different criteria!</p>
        </div>
    @endif

    <!-- Back Button -->
    <div class="text-center">
        <a href="{{ route('home') }}"
           class="inline-flex items-center bg-gray-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Recommendations
        </a>
    </div>
</div>
@endsection
