<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Lume POS') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .nav-item { transition: all 0.15s ease; }
        .nav-item:hover .nav-icon { transform: scale(1.1); }
    </style>
</head>
<body class="bg-slate-50 text-gray-900" x-data="{ sidebarOpen: true }">
    <div class="min-h-screen flex">

        <!-- Sidebar -->
        <aside class="flex flex-col bg-gray-900 text-white transition-all duration-300 ease-in-out shrink-0 relative z-30"
               :class="sidebarOpen ? 'w-64' : 'w-18'">

            <!-- Logo -->
            <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
                <div class="shrink-0 w-9 h-9 bg-indigo-500 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-bolt text-white text-sm"></i>
                </div>
                <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200"
                     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity duration-100"
                     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="overflow-hidden">
                    <p class="font-bold text-white text-base leading-tight whitespace-nowrap">Lume POS</p>
                    <p class="text-xs text-slate-400 whitespace-nowrap">Point of Sale</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <p x-show="sidebarOpen" class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-2">Main Menu</p>

                <a href="{{ route('dashboard') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                          {{ request()->routeIs('dashboard')
                              ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30'
                              : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                    <i class="nav-icon fas fa-chart-pie w-5 text-center shrink-0 text-base"></i>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap overflow-hidden">Dashboard</span>
                </a>

                <a href="{{ route('pos') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                          {{ request()->routeIs('pos')
                              ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30'
                              : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                    <i class="nav-icon fas fa-cash-register w-5 text-center shrink-0 text-base"></i>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap overflow-hidden">Point of Sale</span>
                </a>

                <a href="{{ route('products.index') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                          {{ request()->routeIs('products.*')
                              ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30'
                              : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                    <i class="nav-icon fas fa-box-open w-5 text-center shrink-0 text-base"></i>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap overflow-hidden">Products</span>
                </a>

                <a href="{{ route('orders.index') }}"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                          {{ request()->routeIs('orders.*')
                              ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30'
                              : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                    <i class="nav-icon fas fa-receipt w-5 text-center shrink-0 text-base"></i>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap overflow-hidden">Orders</span>
                </a>
            </nav>

            <!-- User Profile + Logout -->
            <div class="px-3 py-4 border-t border-white/10" x-data="{ userMenu: false }" @click.outside="userMenu = false">
                <button type="button" @click="userMenu = !userMenu"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-white/10 cursor-pointer transition text-left">
                    <div class="shrink-0 w-8 h-8 bg-linear-to-br from-violet-400 to-indigo-600 rounded-full flex items-center justify-center text-xs font-bold text-white uppercase">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div x-show="sidebarOpen" x-transition class="overflow-hidden flex-1 min-w-0">
                        <p class="text-sm font-medium text-white whitespace-nowrap truncate">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-xs text-slate-400 whitespace-nowrap truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <i x-show="sidebarOpen" class="fas fa-chevron-up text-[10px] text-slate-500 shrink-0 transition-transform duration-200"
                       :class="userMenu ? '' : 'rotate-180'"></i>
                </button>

                <!-- Popup menu -->
                <div x-show="userMenu" x-transition
                     class="mt-1 bg-gray-800 rounded-xl border border-white/10 py-1 overflow-hidden">
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-300 hover:bg-white/10 hover:text-white transition-colors">
                        <i class="fas fa-user-circle text-xs w-4 text-center"></i>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Profile</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-colors">
                            <i class="fas fa-right-from-bracket text-xs w-4 text-center"></i>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Log Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Bar -->
            <header class="bg-white border-b border-gray-200 px-6 py-4 shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button type="button" @click="sidebarOpen = !sidebarOpen"
                                class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors">
                            <i class="fas fa-bars text-base"></i>
                        </button>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">@yield('title', 'Dashboard')</h2>
                            <p class="text-xs text-gray-400">@yield('subtitle', '')</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" class="relative w-9 h-9 rounded-lg flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors">
                            <i class="fas fa-bell text-base"></i>
                        </button>
                        <div class="w-px h-6 bg-gray-200"></div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 bg-linear-to-br from-violet-400 to-indigo-600 rounded-full flex items-center justify-center text-xs font-bold text-white uppercase">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                            </div>
                            <span class="text-sm font-medium text-gray-700 hidden sm:block">{{ auth()->user()->name ?? 'User' }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 overflow-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
