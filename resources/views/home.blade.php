@extends('layouts.app')

@section('title', 'Customer Dashboard - Find Perfect Souvenirs')

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
    <!-- Hero Section -->
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
            <i class="fas fa-globe-asia text-indigo-600"></i>
            Find Your Perfect
            <span class="text-indigo-600">Indonesian Souvenirs</span>
        </h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Our AI-powered system recommends the best oleh-oleh (souvenirs) based on your preferences, budget, and purpose.
        </p>

        @if(session('error'))
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <!-- Recommendation Form -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-magic text-indigo-600 mr-3"></i>
                Get Personalized Recommendations
            </h2>

            <form id="recommendationForm" action="{{ route('recommend.submit') }}" method="POST">
                @csrf

                <!-- Age Input -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-user text-indigo-600 mr-2"></i>Your Age
                    </label>
                    <input type="number" name="age" required min="1" max="120"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                           placeholder="Enter your age">
                    @error('age')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Selection -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-briefcase text-indigo-600 mr-2"></i>Status
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="status" value="student" required class="peer sr-only">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition hover:border-indigo-300">
                                <i class="fas fa-graduation-cap text-2xl text-indigo-600 mb-2"></i>
                                <p class="font-semibold">Student</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="status" value="worker" required class="peer sr-only">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition hover:border-indigo-300">
                                <i class="fas fa-briefcase text-2xl text-indigo-600 mb-2"></i>
                                <p class="font-semibold">Worker</p>
                            </div>
                        </label>
                    </div>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Budget Input -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-wallet text-indigo-600 mr-2"></i>Budget (IDR)
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-gray-500 font-semibold">Rp</span>
                        <input type="number" name="budget" required min="0" step="0.01"
                               class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                               placeholder="0.00">
                    </div>
                    @error('budget')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purpose Selection -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-gift text-indigo-600 mr-2"></i>Purpose
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="purpose" value="family" required class="peer sr-only">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition hover:border-indigo-300 text-center">
                                <i class="fas fa-home text-2xl text-indigo-600 mb-2"></i>
                                <p class="font-semibold">Family</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="purpose" value="colleague" required class="peer sr-only">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition hover:border-indigo-300 text-center">
                                <i class="fas fa-users text-2xl text-indigo-600 mb-2"></i>
                                <p class="font-semibold">Colleague</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="purpose" value="partner" required class="peer sr-only">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition hover:border-indigo-300 text-center">
                                <i class="fas fa-heart text-2xl text-indigo-600 mb-2"></i>
                                <p class="font-semibold">Partner</p>
                            </div>
                        </label>
                    </div>
                    @error('purpose')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full bg-indigo-600 text-white font-bold py-4 px-6 rounded-lg hover:bg-indigo-700 transition transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                    <i class="fas fa-sparkles mr-2"></i>
                    Get Recommendations
                </button>
            </form>
        </div>

        <!-- Features Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white rounded-xl p-6 shadow-lg text-center">
                <i class="fas fa-brain text-4xl text-indigo-600 mb-4"></i>
                <h3 class="font-bold text-lg mb-2">AI-Powered</h3>
                <p class="text-gray-600">Advanced Random Forest algorithm for accurate predictions</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-lg text-center">
                <i class="fas fa-clock text-4xl text-indigo-600 mb-4"></i>
                <h3 class="font-bold text-lg mb-2">Instant Results</h3>
                <p class="text-gray-600">Get personalized recommendations in seconds</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-lg text-center">
                <i class="fas fa-hand-holding-heart text-4xl text-indigo-600 mb-4"></i>
                <h3 class="font-bold text-lg mb-2">Curated Selection</h3>
                <p class="text-gray-600">Hand-picked authentic Indonesian souvenirs</p>
            </div>
        </div>

        <!-- Popular Categories -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">
                <i class="fas fa-tags text-indigo-600"></i>
                Popular Categories
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('catalog') }}" class="bg-white rounded-lg p-6 shadow hover:shadow-lg transition hover:bg-indigo-50">
                    <i class="fas fa-tshirt text-3xl text-indigo-600 mb-3"></i>
                    <p class="font-semibold">Batik</p>
                </a>
                <a href="{{ route('catalog') }}" class="bg-white rounded-lg p-6 shadow hover:shadow-lg transition hover:bg-indigo-50">
                    <i class="fas fa-paint-brush text-3xl text-indigo-600 mb-3"></i>
                    <p class="font-semibold">Handicrafts</p>
                </a>
                <a href="{{ route('catalog') }}" class="bg-white rounded-lg p-6 shadow hover:shadow-lg transition hover:bg-indigo-50">
                    <i class="fas fa-cookie text-3xl text-indigo-600 mb-3"></i>
                    <p class="font-semibold">Food</p>
                </a>
                <a href="{{ route('catalog') }}" class="bg-white rounded-lg p-6 shadow hover:shadow-lg transition hover:bg-indigo-50">
                    <i class="fas fa-ring text-3xl text-indigo-600 mb-3"></i>
                    <p class="font-semibold">Jewelry</p>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection