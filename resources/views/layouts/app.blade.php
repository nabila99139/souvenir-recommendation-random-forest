<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Oleh-Oleh Recommendation System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <i class="fas fa-gift text-2xl text-indigo-600"></i>
                        <span class="text-xl font-bold text-gray-800">Oleh-Oleh Indonesia</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600 transition">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <a href="{{ route('catalog') }}" class="text-gray-700 hover:text-indigo-600 transition">
                        <i class="fas fa-th-large"></i> Catalog
                    </a>
                    @if(auth()->check())
                        <span class="text-gray-600">
                            <i class="fas fa-user"></i> {{ auth()->user()->name }}
                        </span>
                        @if(auth()->user()->isRoot())
                            <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition" title="Admin Panel">
                                <i class="fas fa-shield-alt"></i>
                            </a>
                        @endif
                        <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-red-600 transition">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-gray-600">
                <p>&copy; 2026 Oleh-Oleh Indonesia Recommendation System. All rights reserved.</p>
                <p class="text-sm mt-2">Powered by Random Forest ML Model</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>