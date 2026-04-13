@extends('layouts.app')

@section('title', 'New Product')
@section('subtitle', 'Add a product to your catalog')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('products.index') }}" class="hover:text-indigo-600 transition-colors">Products</a>
    <i class="fas fa-chevron-right text-xs"></i>
    <span class="text-gray-700 font-medium">New Product</span>
</div>

<form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data"
      x-data="{ imagePreview: null, isCustomizable: false }">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left column: main details --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Basic Info card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Product Details</h3>

                <div class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Product Name <span class="text-red-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="e.g. Classic Butterscotch"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('name') border-red-400 @enderror">
                        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Category + Price --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Category <span class="text-red-400">*</span></label>
                            <input type="text" name="category" value="{{ old('category') }}" required
                                   placeholder="e.g. Pastry"
                                   list="category-suggestions"
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('category') border-red-400 @enderror">
                            <datalist id="category-suggestions">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                            @error('category')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Base Price (RM) <span class="text-red-400">*</span></label>
                            <input type="number" name="price" value="{{ old('price') }}" required
                                   step="0.01" min="0" placeholder="0.00"
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('price') border-red-400 @enderror">
                            @error('price')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
                        <textarea name="description" rows="3"
                                  placeholder="Optional short description shown on POS…"
                                  class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none">{{ old('description') }}</textarea>
                    </div>

                    {{-- Sort order --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Display Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                               min="0" placeholder="0"
                               class="w-32 px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <p class="text-xs text-gray-400 mt-1">Lower number = appears first in POS</p>
                    </div>
                </div>
            </div>

            {{-- Image card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Product Image</h3>
                <div class="flex items-start gap-5">
                    {{-- Preview --}}
                    <div class="w-28 h-28 rounded-xl border-2 border-dashed border-gray-200 overflow-hidden shrink-0 flex items-center justify-center bg-gray-50">
                        <img x-show="imagePreview" :src="imagePreview" class="w-full h-full object-cover rounded-xl">
                        <i x-show="!imagePreview" class="fas fa-image text-gray-300 text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Upload image</label>
                        <input type="file" name="image" accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                               @change="imagePreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                        <p class="text-xs text-gray-400 mt-1.5">PNG, JPG or JPEG — max 4 MB</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column: settings --}}
        <div class="space-y-5">

            {{-- Availability card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Settings</h3>
                <div class="space-y-4">

                    {{-- Available toggle --}}
                    <label class="flex items-start gap-3 cursor-pointer">
                        <div class="mt-0.5">
                            <input type="hidden" name="is_available" value="0">
                            <input type="checkbox" name="is_available" value="1"
                                   {{ old('is_available', '1') == '1' ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-300">
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Available on POS</p>
                            <p class="text-xs text-gray-400">Show this product for sale in the Point of Sale screen</p>
                        </div>
                    </label>

                    <div class="border-t border-gray-100 pt-4">
                        {{-- Customizable toggle --}}
                        <label class="flex items-start gap-3 cursor-pointer">
                            <div class="mt-0.5">
                                <input type="hidden" name="is_customizable" value="0">
                                <input type="checkbox" name="is_customizable" value="1"
                                       x-model="isCustomizable"
                                       {{ old('is_customizable') ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-300">
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Has Customizations</p>
                                <p class="text-xs text-gray-400">Allows staff to pick variants (toppings, sauce, etc.) when adding to order</p>
                            </div>
                        </label>
                        <div x-show="isCustomizable" x-transition
                             class="mt-3 bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            After saving, you'll be taken to the edit page where you can add variant groups and options.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-3">
                <button type="submit"
                        class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                    Save Product
                </button>
                <a href="{{ route('products.index') }}"
                   class="block text-center w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

@endsection
