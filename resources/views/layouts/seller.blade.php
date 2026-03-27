<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Seller Dashboard') - Souvenir Recommendation System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Seller Dashboard -->
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-emerald-800 min-h-screen fixed left-0 top-0">
            <div class="p-6 border-b border-emerald-700">
                <a href="{{ route('seller.dashboard') }}" class="flex items-center space-x-2">
                    <i class="fas fa-store text-emerald-400 text-2xl"></i>
                    <span class="text-xl font-bold text-white">Seller Panel</span>
                </a>
            </div>

            <nav class="mt-6">
                <ul class="space-y-2 px-4">
                    <li>
                        <a href="{{ route('seller.dashboard') }}" class="flex items-center text-gray-300 hover:bg-emerald-700 hover:text-white p-3 rounded-lg transition {{ request()->routeIs('seller.dashboard') ? 'bg-emerald-600 text-white' : '' }}">
                            <i class="fas fa-chart-line mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('seller.business-profile') }}" class="flex items-center text-gray-300 hover:bg-emerald-700 hover:text-white p-3 rounded-lg transition {{ request()->routeIs('seller.business-profile') ? 'bg-emerald-600 text-white' : '' }}">
                            <i class="fas fa-building mr-3"></i>
                            <span>Business Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('seller.souvenirs') }}" class="flex items-center text-gray-300 hover:bg-emerald-700 hover:text-white p-3 rounded-lg transition {{ request()->routeIs('seller.souvenirs*') ? 'bg-emerald-600 text-white' : '' }}">
                            <i class="fas fa-box mr-3"></i>
                            <span>My Souvenirs</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('seller.leads') }}" class="flex items-center text-gray-300 hover:bg-emerald-700 hover:text-white p-3 rounded-lg transition {{ request()->routeIs('seller.leads') ? 'bg-emerald-600 text-white' : '' }}">
                            <i class="fas fa-eye mr-3"></i>
                            <span>Lead Tracking</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Info -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-emerald-700 bg-emerald-800">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="bg-emerald-500 p-2 rounded-full">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-semibold text-sm">{{ auth()->user()->name }}</p>
                        <p class="text-gray-300 text-xs">{{ auth()->user()->business_name ?? 'Seller' }}</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('home') }}" class="block text-center bg-emerald-600 text-white py-2 rounded-lg hover:bg-emerald-700 transition text-sm">
                        <i class="fas fa-home mr-2"></i>Back to App
                    </a>
                    <form action="{{ route('auth.logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition text-sm">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm p-4 sticky top-0 z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">@yield('title', 'Seller Dashboard')</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                            <i class="fas fa-check-circle text-xs mr-1"></i> Seller
                        </span>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-8 py-4 ml-64">
        <div class="text-center text-gray-600 text-sm">
            <p>&copy; 2026 Souvenir Recommendation System. Seller Panel.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>