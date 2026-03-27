@extends('layouts.seller')

@section('title', 'My Souvenirs')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">My Souvenirs</h2>
            <p class="text-gray-600 mt-1">Manage your product catalog</p>
        </div>
        <a href="{{ route('seller.create-souvenir') }}" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition flex items-center">
            <i class="fas fa-plus mr-2"></i>Add New Souvenir
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                <span class="text-green-800">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                <span class="text-red-800">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Souvenirs Grid -->
    @if($souvenirs->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="fas fa-box-open text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No souvenirs yet</h3>
            <p class="text-gray-500 mb-4">Start by adding your first souvenir to your catalog</p>
            <a href="{{ route('seller.create-souvenir') }}" class="inline-block bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 transition">
                <i class="fas fa-plus mr-2"></i>Add Your First Souvenir
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($souvenirs as $souvenir)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                    <!-- Souvenir Image -->
                    @if($souvenir->image_path || $souvenir->image)
                        <div class="aspect-w-16 aspect-h-12 bg-gray-100 relative">
                            <img
                                src="{{ $souvenir->image ?? $souvenir->image_path }}"
                                alt="{{ $souvenir->name }}"
                                class="w-full h-48 object-cover"
                                onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'"
                            >
                            <!-- Price Badge -->
                            <div class="absolute top-2 right-2 bg-emerald-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                Rp {{ number_format($souvenir->price ?? 0, 0, ',', '.') }}
                            </div>
                            <!-- Views Badge -->
                            <div class="absolute top-2 left-2 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                <i class="fas fa-eye mr-1"></i>{{ number_format($souvenir->views) }}
                            </div>
                        </div>
                    @else
                        <div class="aspect-w-16 aspect-h-12 bg-gray-100 h-48 flex items-center justify-center">
                            <i class="fas fa-image text-gray-300 text-5xl"></i>
                        </div>
                    @endif

                    <!-- Souvenir Details -->
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-1 truncate">{{ $souvenir->name }}</h3>
                        <p class="text-sm text-gray-500 mb-2 truncate">{{ $souvenir->category }}</p>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($souvenir->description, 80) }}</p>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <a href="{{ route('seller.edit-souvenir', $souvenir->id) }}" class="flex-1 text-center bg-blue-50 text-blue-600 px-3 py-2 rounded-lg hover:bg-blue-100 transition text-sm">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <form action="{{ route('seller.delete-souvenir', $souvenir->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="w-full bg-red-50 text-red-600 px-3 py-2 rounded-lg hover:bg-red-100 transition text-sm"
                                    onclick="return confirm('Are you sure you want to delete this souvenir?')"
                                >
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($souvenirs->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $souvenirs->links() }}
            </div>
        @endif
    @endif
</div>
@endsection