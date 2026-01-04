<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'POS System') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900" x-data="{ sidebarOpen: true }">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="bg-white shadow-lg transition-all duration-300 ease-in-out" 
               :class="sidebarOpen ? 'w-64' : 'w-16'">
            <div class="p-6" :class="sidebarOpen ? '' : 'px-0'">
                <h1 class="text-2xl font-bold text-gray-800 transition-opacity duration-200" 
                    x-show="sidebarOpen"
                    x-transition>POS System</h1>
                <div class="text-center" x-show="!sidebarOpen">
                    <i class="fas fa-cash-register text-2xl text-gray-700"></i>
                </div>
            </div>
            <nav class="mt-6">
                <a href="{{ route('dashboard') }}" 
                   class="block px-6 py-3 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : '' }}"
                   :class="sidebarOpen ? '' : 'px-0 text-center'"
                   :title="sidebarOpen ? '' : 'Dashboard'">
                    <i class="fas fa-tachometer-alt" :class="sidebarOpen ? 'mr-2' : ''"></i>
                    <span x-show="sidebarOpen" x-transition>Dashboard</span>
                </a>
                <a href="{{ route('pos') }}" 
                   class="block px-6 py-3 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('pos') ? 'bg-blue-50 text-blue-700' : '' }}"
                   :class="sidebarOpen ? '' : 'px-0 text-center'"
                   :title="sidebarOpen ? '' : 'POS'">
                    <i class="fas fa-cash-register" :class="sidebarOpen ? 'mr-2' : ''"></i>
                    <span x-show="sidebarOpen" x-transition>POS</span>
                </a>
                <a href="{{ route('products') }}" 
                   class="block px-6 py-3 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('products') ? 'bg-blue-50 text-blue-700' : '' }}"
                   :class="sidebarOpen ? '' : 'px-0 text-center'"
                   :title="sidebarOpen ? '' : 'Products'">
                    <i class="fas fa-box" :class="sidebarOpen ? 'mr-2' : ''"></i>
                    <span x-show="sidebarOpen" x-transition>Products</span>
                </a>
                <a href="{{ route('orders') }}" 
                   class="block px-6 py-3 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('orders') ? 'bg-blue-50 text-blue-700' : '' }}"
                   :class="sidebarOpen ? '' : 'px-0 text-center'"
                   :title="sidebarOpen ? '' : 'Orders'">
                    <i class="fas fa-receipt" :class="sidebarOpen ? 'mr-2' : ''"></i>
                    <span x-show="sidebarOpen" x-transition>Orders</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm px-4 md:px-6 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <button type="button" @click="sidebarOpen = !sidebarOpen" 
                                class="p-2 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition-colors">
                            <i class="fas fa-bars text-xl text-gray-700"></i>
                        </button>
                        <h2 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600 hidden md:inline">Welcome, John Doe</span>
                        <button type="button" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 active:bg-red-700">Logout</button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 h-full">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>