@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Orders</h3>
    <div class="space-y-4">
        @foreach ([
            ['id' => 1001, 'items' => 2, 'total' => 45.99, 'status' => 'Completed'],
            ['id' => 1000, 'items' => 5, 'total' => 127.50, 'status' => 'Completed'],
            ['id' => 999, 'items' => 1, 'total' => 12.99, 'status' => 'Completed'],
            ['id' => 998, 'items' => 3, 'total' => 32.50, 'status' => 'Pending'],
            ['id' => 997, 'items' => 4, 'total' => 78.00, 'status' => 'Completed']
        ] as $order)
            <div class="flex justify-between items-center py-2 border-b">
                <div>
                    <p class="font-medium">Order #{{ $order['id'] }}</p>
                    <p class="text-sm text-gray-600">{{ $order['items'] }} items • ${{ number_format($order['total'], 2) }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm {{ $order['status'] === 'Completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $order['status'] }}</span>
            </div>
        @endforeach
    </div>
</div>
@endsection