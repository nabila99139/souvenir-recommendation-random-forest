@extends('layouts.seller')

@section('title', 'Business Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Business Profile</h2>
        <p class="text-gray-600 mt-1">Update your business information and contact details</p>
    </div>

    <!-- Business Profile Form -->
    <div class="bg-white rounded-lg shadow-sm">
        <form action="{{ route('seller.update-business-profile') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <span class="text-green-800">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

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

            <!-- Business Information -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-building text-emerald-600 mr-2"></i>Business Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Business Name -->
                    <div>
                        <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Business Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="business_name"
                            name="business_name"
                            value="{{ old('business_name', $user->business_name ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            placeholder="Your Business Name"
                        >
                        @error('business_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Business Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Business Phone <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $user->business_phone ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            placeholder="+62 812 3456 7890"
                        >
                        @error('phone')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Business Address -->
                <div class="mt-4">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                        Business Address <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Your complete business address"
                    >{{ old('address', $user->business_address ?? '') }}</textarea>
                    @error('address')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Business Description -->
                <div class="mt-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Business Description <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Describe your business, products, and what makes you unique"
                    >{{ old('description', $user->business_description ?? '') }}</textarea>
                    <p class="text-gray-500 text-sm mt-1">
                        <i class="fas fa-info-circle"></i> This helps customers understand your business and products
                    </p>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Business Hours -->
                <div class="mt-4">
                    <label for="business_hours" class="block text-sm font-medium text-gray-700 mb-1">
                        Business Hours
                    </label>
                    <input
                        type="text"
                        id="business_hours"
                        name="business_hours"
                        value="{{ old('business_hours', $user->business_hours ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Mon-Fri: 9AM-5PM, Sat-Sun: 10AM-3PM"
                    >
                    <p class="text-gray-500 text-sm mt-1">
                        <i class="fas fa-info-circle"></i> Let customers know when you're available
                    </p>
                    @error('business_hours')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Current User Info -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user text-emerald-600 mr-2"></i>Account Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                        <input
                            type="email"
                            value="{{ $user->email }}"
                            disabled
                            class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                        >
                        <p class="text-gray-500 text-sm mt-1">
                            <i class="fas fa-info-circle"></i> Contact admin to change email
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <input
                            type="text"
                            value="{{ $user->getRoleDisplayName() }}"
                            disabled
                            class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                        >
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6">
                <a href="{{ route('seller.dashboard') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
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