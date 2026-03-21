@extends('layouts.app')

@section('title', 'Souvenir Catalog')

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Catalog Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            <i class="fas fa-globe-asia text-indigo-600"></i>
            Complete Souvenir Catalog
        </h1>
        <p class="text-xl text-gray-600">
            Browse our collection of authentic Indonesian souvenirs
        </p>
    </div>

    <!-- Search and Filter -->
    <div class="max-w-4xl mx-auto mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-search text-indigo-600 mr-2"></i>Search
                    </label>
                    <input type="text" id="search" placeholder="Search souvenirs..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-filter text-indigo-600 mr-2"></i>Category
                    </label>
                    <select id="categoryFilter"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        <option value="">All Categories</option>
                        <option value="batik">Batik</option>
                        <option value="handicrafts">Handicrafts</option>
                        <option value="food">Food</option>
                        <option value="textiles">Textiles</option>
                        <option value="jewelry">Jewelry</option>
                        <option value="home_decor">Home Decor</option>
                        <option value="coffee_spices">Coffee & Spices</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Tabs -->
    <div class="flex flex-wrap justify-center gap-2 mb-8">
        <button onclick="filterByCategory('')"
                class="category-tab px-4 py-2 rounded-full bg-indigo-600 text-white font-semibold transition hover:bg-indigo-700"
                data-category="">
            All
        </button>
        <button onclick="filterByCategory('batik')"
                class="category-tab px-4 py-2 rounded-full bg-white text-gray-700 font-semibold transition hover:bg-indigo-100"
                data-category="batik">
            Batik
        </button>
        <button onclick="filterByCategory('handicrafts')"
                class="category-tab px-4 py-2 rounded-full bg-white text-gray-700 font-semibold transition hover:bg-indigo-100"
                data-category="handicrafts">
            Handicrafts
        </button>
        <button onclick="filterByCategory('food')"
                class="category-tab px-4 py-2 rounded-full bg-white text-gray-700 font-semibold transition hover:bg-indigo-100"
                data-category="food">
            Food
        </button>
        <button onclick="filterByCategory('textiles')"
                class="category-tab px-4 py-2 rounded-full bg-white text-gray-700 font-semibold transition hover:bg-indigo-100"
                data-category="textiles">
            Textiles
        </button>
        <button onclick="filterByCategory('jewelry')"
                class="category-tab px-4 py-2 rounded-full bg-white text-gray-700 font-semibold transition hover:bg-indigo-100"
                data-category="jewelry">
            Jewelry
        </button>
        <button onclick="filterByCategory('home_decor')"
                class="category-tab px-4 py-2 rounded-full bg-white text-gray-700 font-semibold transition hover:bg-indigo-100"
                data-category="home_decor">
            Home Decor
        </button>
        <button onclick="filterByCategory('coffee_spices')"
                class="category-tab px-4 py-2 rounded-full bg-white text-gray-700 font-semibold transition hover:bg-indigo-100"
                data-category="coffee_spices">
            Coffee & Spices
        </button>
    </div>

    <!-- Souvenir Grid -->
    <div id="souvenirGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($souvenirs as $souvenir)
            <div class="souvenir-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:scale-105"
                 data-name="{{ strtolower($souvenir->name) }}"
                 data-category="{{ $souvenir->category }}">
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
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {{ $souvenir->description }}
                        </p>
                    @endif

                    <button class="w-full bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-info-circle mr-2"></i>View Details
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- No Results Message -->
    <div id="noResults" class="text-center py-12 hidden">
        <i class="fas fa-search text-6xl text-gray-400 mb-4"></i>
        <h3 class="text-2xl font-bold text-gray-700 mb-2">No souvenirs found</h3>
        <p class="text-gray-500">Try adjusting your search or filter criteria.</p>
    </div>
</div>

@push('scripts')
<script>
// Category filter functionality
function filterByCategory(category) {
    const cards = document.querySelectorAll('.souvenir-card');
    const tabs = document.querySelectorAll('.category-tab');

    // Update tab styles
    tabs.forEach(tab => {
        if (tab.dataset.category === category) {
            tab.classList.remove('bg-white', 'text-gray-700');
            tab.classList.add('bg-indigo-600', 'text-white');
        } else {
            tab.classList.remove('bg-indigo-600', 'text-white');
            tab.classList.add('bg-white', 'text-gray-700');
        }
    });

    // Filter cards
    let visibleCount = 0;
    cards.forEach(card => {
        if (category === '' || card.dataset.category === category) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });

    // Show/hide no results message
    const noResults = document.getElementById('noResults');
    if (visibleCount === 0) {
        noResults.classList.remove('hidden');
    } else {
        noResults.classList.add('hidden');
    }
}

// Search functionality
document.getElementById('search').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.souvenir-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const name = card.dataset.name;
        if (name.includes(searchTerm)) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });

    // Show/hide no results message
    const noResults = document.getElementById('noResults');
    if (visibleCount === 0) {
        noResults.classList.remove('hidden');
    } else {
        noResults.classList.add('hidden');
    }
});
</script>
@endpush
@endsection
