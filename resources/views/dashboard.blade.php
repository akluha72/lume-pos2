@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Today's Sales -->
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-full">
                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Today's Sales</p>
                <p class="text-2xl font-bold text-gray-800">$1,247.89</p>
            </div>
        </div>
    </div>

    <!-- Orders Today -->
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-full">
                <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Orders Today</p>
                <p class="text-2xl font-bold text-gray-800">47</p>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Low Stock Items</p>
                <p class="text-2xl font-bold text-gray-800">3</p>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-full">
                <i class="fas fa-box text-purple-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Total Products</p>
                <p class="text-2xl font-bold text-gray-800">156</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Recent Orders</h3>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            <div class="flex justify-between items-center py-2 border-b">
                <div>
                    <p class="font-medium">Order #1001</p>
                    <p class="text-sm text-gray-600">2 items • $45.99</p>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Completed</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b">
                <div>
                    <p class="font-medium">Order #1000</p>
                    <p class="text-sm text-gray-600">5 items • $127.50</p>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Completed</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b">
                <div>
                    <p class="font-medium">Order #999</p>
                    <p class="text-sm text-gray-600">1 item • $12.99</p>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Completed</span>
            </div>
        </div>
    </div>
</div>
@endsection