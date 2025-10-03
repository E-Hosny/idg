<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'IDG Items Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }
        .font-arabic {
            font-family: 'Tajawal', sans-serif;
        }
        .sidebar-link {
            @apply flex items-center px-6 py-3 text-gray-300 hover:bg-green-700 hover:text-white transition-colors duration-200;
        }
        .sidebar-link.active {
            @apply bg-green-700 text-white border-r-4 border-white;
        }
        .card {
            @apply bg-white overflow-hidden shadow rounded-lg;
        }
        .card-header {
            @apply px-4 py-5 sm:px-6 border-b border-gray-200;
        }
        .card-body {
            @apply px-4 py-5 sm:p-6;
        }
        .btn-primary {
            @apply inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150;
        }
        .btn-secondary {
            @apply inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}" 
      x-data="{ sidebarOpen: false }" 
      x-cloak>
    
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col flex-grow pt-5 overflow-y-auto bg-gradient-to-b from-green-800 to-green-900">
                    <!-- Logo -->
                    <div class="flex items-center flex-shrink-0 px-6 py-4">
                        <img class="w-12 h-12" src="{{ asset('images/idg-logo.png') }}" alt="IDG">
                        <div class="{{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }}">
                            <h1 class="text-xl font-bold text-white">IDG</h1>
                            <p class="text-sm text-green-200">{{ __('Items Dashboard') }}</p>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav class="mt-5 flex-1">
                        <div class="space-y-1">
                            <a href="{{ route('dashboard') }}" 
                               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt w-5 h-5 ml-3"></i>
                                <span class="ml-3">{{ __('Dashboard') }}</span>
                            </a>

                            <a href="{{ route('dashboard.artifacts') }}" 
                               class="sidebar-link {{ request()->routeIs('dashboard.artifacts*') ? 'active' : '' }}">
                                <i class="fas fa-gem w-5 h-5 ml-3"></i>
                                <span class="ml-3">{{ __('Items') }}</span>
                            </a>

                            <a href="{{ route('dashboard.evaluations') }}" 
                               class="sidebar-link {{ request()->routeIs('dashboard.evaluations*') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-check w-5 h-5 ml-3"></i>
                                <span class="ml-3">{{ __('Evaluations') }}</span>
                            </a>

                            <a href="{{ route('dashboard.categories') }}" 
                               class="sidebar-link {{ request()->routeIs('dashboard.categories*') ? 'active' : '' }}">
                                <i class="fas fa-tags w-5 h-5 ml-3"></i>
                                <span class="ml-3">{{ __('Categories') }}</span>
                            </a>

                            <a href="{{ route('dashboard.analytics') }}" 
                               class="sidebar-link {{ request()->routeIs('dashboard.analytics*') ? 'active' : '' }}">
                                <i class="fas fa-chart-line w-5 h-5 ml-3"></i>
                                <span class="ml-3">{{ __('Analytics') }}</span>
                            </a>
                        </div>
                    </nav>

                    <!-- User Menu -->
                    <div class="flex-shrink-0 flex border-t border-green-700 p-4">
                        <div class="flex items-center w-full">
                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div class="ml-3 min-w-0 flex-1">
                                <p class="text-sm font-medium text-white truncate">
                                    {{ Auth::user()->name }}
                                </p>
                                <p class="text-xs text-green-200 truncate">
                                    {{ Auth::user()->email }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 lg:hidden"
             @click="sidebarOpen = false">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
        </div>

        <!-- Main content -->
        <div class="flex-1 overflow-hidden">
            <!-- Top navigation -->
            <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true"
                        class="px-4 border-r border-gray-200 text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500 lg:hidden">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Top nav content -->
                <div class="flex-1 px-4 flex justify-between items-center">
                    <div class="flex-1">
                        <h1 class="text-2xl font-semibold text-gray-900">
                            @yield('page-title', __('Dashboard'))
                        </h1>
                    </div>

                    <div class="ml-4 flex items-center md:ml-6 space-x-4">
                        <!-- Language Switcher -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center text-sm text-gray-700 hover:text-gray-900 focus:outline-none">
                                <i class="fas fa-globe mr-2"></i>
                                {{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}
                                <i class="fas fa-chevron-down ml-1"></i>
                            </button>
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('lang.switch', 'en') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-flag mr-2"></i> English
                                </a>
                                <a href="{{ route('lang.switch', 'ar') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-flag mr-2"></i> العربية
                                </a>
                            </div>
                        </div>

                        <!-- User menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center text-sm text-gray-700 hover:text-gray-900 focus:outline-none">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <span class="ml-2">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down ml-1"></i>
                            </button>
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> {{ __('Profile') }}
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i> {{ __('Settings') }}
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <!-- Page content -->
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Initialize any JavaScript functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.display = 'none';
                });
            }, 5000);
        });
    </script>

    @stack('scripts')
</body>
</html> 