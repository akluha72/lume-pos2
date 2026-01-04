@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Products</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ([
            ['id' => 1, 'name' => 'Coffee', 'price' => 3.50],
            ['id' => 2, 'name' => 'Latte', 'price' => 4.25],
            ['id' => 3, 'name' => 'Cappuccino', 'price' => 4.75],
            ['id' => 4, 'name' => 'Espresso', 'price' => 2.50],
            ['id' => 5, 'name' => 'Croissant', 'price' => 2.75],
            ['id' => 6, 'name' => 'Muffin', 'price' => 3.00],
            ['id' => 7, 'name' => 'Sandwich', 'price' => 7.50],
            ['id' => 8, 'name' => 'Salad', 'price' => 8.25],
            ['id' => 9, 'name' => 'Burger', 'price' => 9.50],
            ['id' => 10, 'name' => 'Fries', 'price' => 4.00],
            ['id' => 11, 'name' => 'Pizza Slice', 'price' => 5.75],
            ['id' => 12, 'name' => 'Ice Cream', 'price' => 3.25]
        ] as $product)
            <div class="bg-gray-50 p-4 rounded-lg text-center">
                <div class="w-16 h-16 bg-gray-200 rounded-full mx-auto mb-2 flex items-center justify-center">
                    <i class="fas fa-box text-gray-500 text-2xl"></i>
                </div>
                <h4 class="font-medium text-sm">{{ $product['name'] }}</h4>
                <p class="text-green-600 font-bold">${{ number_format($product['price'], 2) }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection