@extends('layouts.app')

@section('title', 'Products')
@section('subtitle', 'Manage your menu catalog')

@section('content')

{{-- Header row --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        {{-- Stats pills --}}
        <span class="text-xs font-semibold text-gray-500 bg-white border border-gray-200 px-3 py-1.5 rounded-full shadow-sm">
            {{ $products->count() }} total
        </span>
        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full">
            {{ $products->where('is_available', true)->count() }} available
        </span>
        @if($products->where('is_available', false)->count())
        <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full">
            {{ $products->where('is_available', false)->count() }} hidden
        </span>
        @endif
    </div>
    <a href="{{ route('products.create') }}"
       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition-colors">
        <i class="fas fa-plus text-xs"></i> Add Product
    </a>
</div>

{{-- Flash message --}}
@if(session('success'))
<div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium px-4 py-3 rounded-xl mb-5"
     x-data x-init="setTimeout(() => $el.remove(), 4000)">
    <i class="fas fa-circle-check text-emerald-500"></i>
    {{ session('success') }}
</div>
@endif

{{-- Filters --}}
<form method="GET" action="{{ route('products.index') }}"
      class="flex flex-col sm:flex-row gap-3 mb-6">
    <div class="relative flex-1">
        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search products…"
               class="w-full pl-10 pr-4 py-2.5 text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 shadow-sm">
    </div>
    <select name="category"
            class="text-sm bg-white border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-300 shadow-sm"
            onchange="this.form.submit()">
        <option value="all">All categories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
        @endforeach
    </select>
    <select name="availability"
            class="text-sm bg-white border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-300 shadow-sm"
            onchange="this.form.submit()">
        <option value="">All status</option>
        <option value="available"   {{ request('availability') === 'available'   ? 'selected' : '' }}>Available</option>
        <option value="unavailable" {{ request('availability') === 'unavailable' ? 'selected' : '' }}>Hidden</option>
    </select>
    @if(request()->hasAny(['search','category','availability']))
    <a href="{{ route('products.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 bg-white border border-gray-200 rounded-xl px-3 py-2.5 flex items-center gap-1.5 shadow-sm">
        <i class="fas fa-xmark"></i> Clear
    </a>
    @endif
</form>

{{-- Product grid --}}
@if($products->isEmpty())
<div class="flex flex-col items-center justify-center py-24 text-center">
    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
        <i class="fas fa-box-open text-gray-400 text-2xl"></i>
    </div>
    <p class="text-gray-500 font-medium">No products found</p>
    <p class="text-sm text-gray-400 mt-1">Try a different search or <a href="{{ route('products.create') }}" class="text-indigo-600 hover:underline">add a new product</a>.</p>
</div>
@else
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
    @foreach($products as $product)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col group hover:shadow-md transition-shadow">

        {{-- Image --}}
        <div class="relative">
            @if($product->image)
                <img src="{{ $product->image }}" alt="{{ $product->name }}"
                     class="w-full h-36 object-cover">
            @else
                <div class="w-full h-36 bg-gradient-to-br from-indigo-100 to-violet-100 flex items-center justify-center">
                    <i class="fas fa-image text-indigo-300 text-3xl"></i>
                </div>
            @endif

            {{-- Badges --}}
            <div class="absolute top-2 left-2 flex flex-col gap-1">
                @if(!$product->is_available)
                <span class="text-[10px] font-bold bg-gray-800/80 text-white px-2 py-0.5 rounded-full backdrop-blur-sm">Hidden</span>
                @endif
                @if($product->is_customizable)
                <span class="text-[10px] font-bold bg-amber-400 text-amber-900 px-2 py-0.5 rounded-full">Custom</span>
                @endif
            </div>

            {{-- Hover actions --}}
            <div class="absolute inset-0 bg-gray-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                <a href="{{ route('products.edit', $product) }}"
                   class="w-9 h-9 bg-white rounded-lg flex items-center justify-center text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors shadow">
                    <i class="fas fa-pen text-sm"></i>
                </a>
                <form method="POST" action="{{ route('products.destroy', $product) }}"
                      onsubmit="return confirm('Delete \'{{ addslashes($product->name) }}\'? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-9 h-9 bg-white rounded-lg flex items-center justify-center text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors shadow">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- Info --}}
        <div class="p-3 flex-1 flex flex-col gap-1">
            <p class="text-sm font-semibold text-gray-800 truncate">{{ $product->name }}</p>
            <div class="flex items-center justify-between mt-auto pt-1">
                <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full">{{ $product->category }}</span>
                <span class="text-sm font-bold text-indigo-600">RM {{ number_format($product->price, 2) }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
