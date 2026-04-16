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

<script>
function categoryDropdown() {
    return {
        open: false,
        query: '{{ old('category') }}',
        categories: @json($categories->values()),
        get filtered() {
            return this.query
                ? this.categories.filter(c => c.toLowerCase().includes(this.query.toLowerCase()))
                : this.categories;
        },
        get isNew() {
            return this.query && !this.categories.some(c => c.toLowerCase() === this.query.toLowerCase());
        },
        select(cat) { this.query = cat; this.open = false; }
    };
}
</script>

<form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data"
      x-data="{ imagePreview: null }">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left column: main details --}}
        <div class="lg:col-span-2">

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Product Details</h3>

                {{-- Name + Image side by side --}}
                <div class="flex items-start gap-4 mb-4">

                    {{-- Product Name --}}
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Product Name <span class="text-red-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="e.g. Classic Butterscotch"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('name') border-red-400 @enderror">
                        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Clickable image preview --}}
                    <div class="shrink-0 flex flex-col items-center gap-1">
                        <div class="w-24 h-24 rounded-xl border-2 border-dashed border-gray-200 overflow-hidden flex items-center justify-center bg-gray-50 cursor-pointer hover:border-indigo-300 hover:bg-indigo-50/30 transition-colors"
                             title="Click to upload image"
                             @click="$refs.imageInput.click()">
                            <img x-show="imagePreview" :src="imagePreview" class="w-full h-full object-cover rounded-xl">
                            <div x-show="!imagePreview" class="flex flex-col items-center gap-1 text-gray-300">
                                <i class="fas fa-image text-2xl"></i>
                                <span class="text-[10px] font-medium">Photo</span>
                            </div>
                        </div>
                        <input type="file" name="image" accept="image/*" x-ref="imageInput" class="hidden"
                               @change="imagePreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                        <span class="text-[10px] text-gray-400">PNG/JPG · 4MB</span>
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- Category + Price --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div x-data="categoryDropdown()" @click.outside="open = false" class="relative">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Category <span class="text-red-400">*</span></label>
                            <input type="text" name="category" x-model="query" required autocomplete="off"
                                   placeholder="Select or type new…"
                                   @focus="open = true"
                                   @input="open = true"
                                   @keydown.escape="open = false"
                                   @keydown.enter.prevent="filtered[0] && !isNew ? select(filtered[0]) : open = false"
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('category') border-red-400 @enderror">
                            @error('category')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            {{-- Dropdown --}}
                            <div x-show="open && (filtered.length > 0 || isNew)"
                                 x-transition:enter="transition duration-100"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute z-30 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                                <div class="max-h-44 overflow-y-auto">
                                    <template x-for="cat in filtered" :key="cat">
                                        <button type="button" @click="select(cat)"
                                                class="w-full text-left px-4 py-2.5 text-sm transition-colors"
                                                :class="query === cat ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                                x-text="cat"></button>
                                    </template>
                                </div>
                                <template x-if="isNew">
                                    <div class="border-t border-gray-100">
                                        <button type="button" @click="select(query)"
                                                class="w-full text-left px-4 py-2.5 text-sm text-indigo-600 hover:bg-indigo-50 font-medium flex items-center gap-1.5 transition-colors">
                                            <i class="fas fa-plus text-xs"></i>
                                            Create "<span x-text="query" class="font-bold"></span>"
                                        </button>
                                    </div>
                                </template>
                            </div>
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
                                  placeholder="Optional short description…"
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
        </div>

        {{-- Right column: settings + actions --}}
        <div class="space-y-5">

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Settings</h3>
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
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-3">
                <button type="submit"
                        class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                    Add Product
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
