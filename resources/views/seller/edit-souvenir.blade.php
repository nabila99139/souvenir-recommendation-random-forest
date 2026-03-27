@extends('layouts.seller')

@section('title', 'Edit Souvenir')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Edit Souvenir</h2>
        <p class="text-gray-600 mt-1">Update your souvenir details</p>
    </div>

    <!-- Edit Souvenir Form -->
    <div class="bg-white rounded-lg shadow-sm">
        <form action="{{ route('seller.update-souvenir', $souvenir->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <!-- Error Messages -->
            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                        <span class="text-red-800 font-semibold">Please fix the following errors:</span>
                    </div>
                    <ul class="text-red-700 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Current Image Preview -->
            @if($souvenir->image_path || $souvenir->image)
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Current Image</h3>
                    <div class="flex items-center space-x-4">
                        <img
                            src="{{ $souvenir->image ?? $souvenir->image_path }}"
                            alt="{{ $souvenir->name }}"
                            class="w-32 h-32 object-cover rounded-lg"
                            onerror="this.src='https://via.placeholder.com/128x128?text=No+Image'"
                        >
                        <div>
                            <p class="font-semibold text-gray-900">{{ $souvenir->name }}</p>
                            <p class="text-sm text-gray-500">Views: {{ number_format($souvenir->views) }}</p>
                            <p class="text-sm text-gray-500">Price: Rp {{ number_format($souvenir->price ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Basic Information -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-emerald-600 mr-2"></i>Basic Information
                </h3>

                <!-- Souvenir Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Souvenir Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $souvenir->name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div class="mb-4">
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="category"
                        name="category"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                        <option value="">Select a category</option>
                        <option value="crafts" {{ old('category', $souvenir->category) === 'crafts' ? 'selected' : '' }}>Crafts & Handicrafts</option>
                        <option value="clothing" {{ old('category', $souvenir->category) === 'clothing' ? 'selected' : '' }}>Clothing & Textiles</option>
                        <option value="food" {{ old('category', $souvenir->category) === 'food' ? 'selected' : '' }}>Food & Spices</option>
                        <option value="jewelry" {{ old('category', $souvenir->category) === 'jewelry' ? 'selected' : '' }}>Jewelry & Accessories</option>
                        <option value="art" {{ old('category', $souvenir->category) === 'art' ? 'selected' : '' }}>Art & Decorations</option>
                        <option value="other" {{ old('category', $souvenir->category) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div class="mb-4">
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                        Price (IDR) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                        <input
                            type="number"
                            id="price"
                            name="price"
                            value="{{ old('price', $souvenir->price) }}"
                            step="0.01"
                            min="0"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                    </div>
                    @error('price')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    >{{ old('description', $souvenir->description) }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Image Upload -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-image text-emerald-600 mr-2"></i>Update Image (Optional)
                </h3>

                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                        New Product Image
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label
                            for="image"
                            class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition"
                        >
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-2"></i>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-gray-500">PNG, JPG, JPEG, GIF (MAX. 2MB)</p>
                                <p class="text-xs text-gray-400 mt-2">Leave empty to keep current image</p>
                            </div>
                            <input id="image" type="file" name="image" class="hidden" accept="image/*">
                        </label>
                    </div>
                    @error('image')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6">
                <a href="{{ route('seller.souvenirs') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition flex items-center"
                >
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection