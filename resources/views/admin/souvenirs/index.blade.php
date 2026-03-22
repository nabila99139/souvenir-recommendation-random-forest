@extends('layouts.admin')

@section('title', 'Souvenir Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">
            <i class="fas fa-gift text-indigo-600 mr-3"></i>Souvenir Management
        </h2>
        <a href="{{ route('admin.souvenirs.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-plus mr-2"></i>Add Souvenir
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Search Souvenirs</label>
                <input type="text" placeholder="Search by name..."
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Category</label>
                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    <option value="batik">Batik</option>
                    <option value="handicrafts">Handicrafts</option>
                    <option value="food">Food</option>
                    <option value="textiles">Textiles</option>
                    <option value="jewelry">Jewelry</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Price Range</label>
                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">All Ranges</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Souvenirs Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($souvenirs as $souvenir)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:scale-105">
                <!-- Image Placeholder -->
                <div class="bg-gradient-to-br from-indigo-100 to-purple-100 h-48 flex items-center justify-center">
                    @if($souvenir->image_path)
                        <img src="{{ asset($souvenir->image_path) }}" alt="{{ $souvenir->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="text-center">
                            <i class="fas fa-gift text-6xl text-indigo-400"></i>
                        </div>
                    @endif
                </div>

                <!-- Content -->
                <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-lg text-gray-900">{{ $souvenir->name }}</h3>
                        @if($souvenir->price_range === 'low')
                            <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full">
                                Low
                            </span>
                        @elseif($souvenir->price_range === 'medium')
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full">
                                Medium
                            </span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded-full">
                                High
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <span class="inline-block bg-indigo-100 text-indigo-800 text-sm px-3 py-1 rounded-full">
                            <i class="fas fa-tag mr-1"></i>{{ ucfirst($souvenir->category) }}
                        </span>
                    </div>

                    <div class="flex space-x-2 mt-4">
                        <a href="{{ route('admin.souvenirs.edit', $souvenir) }}"
                           class="flex-1 bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-indigo-700 transition text-center">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                        <form action="{{ route('admin.souvenirs.destroy', $souvenir) }}" method="POST" class="flex-1"
                              onsubmit="return confirm('Are you sure you want to delete this souvenir?');">
                            @csrf
                            <button type="submit" class="w-full bg-red-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-trash mr-2"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-gift text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-700 mb-2">No Souvenirs Found</h3>
                <p class="text-gray-500">Get started by adding your first souvenir.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($souvenirs->hasPages())
        <div class="mt-6">
            {{ $souvenirs->links() }}
        </div>
    @endif
</div>
@endsection
