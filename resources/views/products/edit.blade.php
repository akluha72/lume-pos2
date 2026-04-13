@extends('layouts.app')

@section('title', 'Edit Product')
@section('subtitle', $product->name)

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('products.index') }}" class="hover:text-indigo-600 transition-colors">Products</a>
    <i class="fas fa-chevron-right text-xs"></i>
    <span class="text-gray-700 font-medium truncate max-w-xs">{{ $product->name }}</span>
</div>

{{-- Flash --}}
@if(session('success'))
<div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium px-4 py-3 rounded-xl mb-5"
     x-data x-init="setTimeout(() => $el.remove(), 4000)">
    <i class="fas fa-circle-check text-emerald-500"></i>
    {{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data"
      x-data="{ imagePreview: null }">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- ── Left: main details ── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Basic Info --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Product Details</h3>
                <div class="space-y-4">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Product Name <span class="text-red-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('name') border-red-400 @enderror">
                        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Category <span class="text-red-400">*</span></label>
                            <input type="text" name="category" value="{{ old('category', $product->category) }}" required
                                   list="category-suggestions"
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('category') border-red-400 @enderror">
                            <datalist id="category-suggestions">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Base Price (RM) <span class="text-red-400">*</span></label>
                            <input type="number" name="price" value="{{ old('price', $product->price) }}" required
                                   step="0.01" min="0"
                                   class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('price') border-red-400 @enderror">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Display Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}"
                               min="0"
                               class="w-32 px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <p class="text-xs text-gray-400 mt-1">Lower = appears first in POS</p>
                    </div>
                </div>
            </div>

            {{-- Image --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Product Image</h3>
                <div class="flex items-start gap-5">
                    <div class="w-28 h-28 rounded-xl border-2 border-dashed border-gray-200 overflow-hidden shrink-0 flex items-center justify-center bg-gray-50">
                        <img x-show="imagePreview" :src="imagePreview" class="w-full h-full object-cover rounded-xl">
                        @if($product->image)
                            <img x-show="!imagePreview" src="{{ $product->image }}" alt="{{ $product->name }}"
                                 class="w-full h-full object-cover rounded-xl">
                        @else
                            <i x-show="!imagePreview" class="fas fa-image text-gray-300 text-3xl"></i>
                        @endif
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Replace image</label>
                        <input type="file" name="image" accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                               @change="imagePreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                        <p class="text-xs text-gray-400 mt-1.5">Leave blank to keep current image</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Right: settings + actions ── --}}
        <div class="space-y-5">

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Settings</h3>
                <div class="space-y-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <div class="mt-0.5">
                            <input type="hidden" name="is_available" value="0">
                            <input type="checkbox" name="is_available" value="1"
                                   {{ $product->is_available ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-300">
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Available on POS</p>
                            <p class="text-xs text-gray-400">Show this product in the POS screen</p>
                        </div>
                    </label>
                    <div class="border-t border-gray-100 pt-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <div class="mt-0.5">
                                <input type="hidden" name="is_customizable" value="0">
                                <input type="checkbox" name="is_customizable" value="1"
                                       {{ $product->is_customizable ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-300">
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Has Customizations</p>
                                <p class="text-xs text-gray-400">Enables variant picker in POS</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-3">
                <button type="submit"
                        class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                    Save Changes
                </button>
                <a href="{{ route('products.index') }}"
                   class="block text-center w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                    Back to Products
                </a>
                <div class="border-t border-gray-100 pt-3">
                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                          onsubmit="return confirm('Permanently delete \'{{ addslashes($product->name) }}\'?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-full py-2 text-red-500 hover:text-red-700 text-sm font-medium flex items-center justify-center gap-2 hover:bg-red-50 rounded-xl transition-colors">
                            <i class="fas fa-trash text-xs"></i> Delete Product
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- ════════════════════════════════════════════════════════════════════════
     VARIANT GROUPS SECTION — only meaningful when is_customizable is on
     but we show it always so staff can prepare groups before enabling.
     ════════════════════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Variant Groups</h3>
            <p class="text-xs text-gray-400 mt-0.5">
                Options staff can pick from when adding this product to an order
                @if(!$product->is_customizable)
                    — <span class="text-amber-600 font-medium">enable "Has Customizations" above to activate these in POS</span>
                @endif
            </p>
        </div>
        <span class="text-xs bg-indigo-50 text-indigo-600 font-semibold px-2.5 py-1 rounded-full">
            {{ $product->variantGroups->count() }} {{ Str::plural('group', $product->variantGroups->count()) }}
        </span>
    </div>

    @if($product->variantGroups->isEmpty())
    <div class="px-6 py-10 text-center text-gray-400">
        <i class="fas fa-layer-group text-3xl mb-3 text-gray-200"></i>
        <p class="text-sm">No variant groups yet.</p>
        <p class="text-xs mt-1">Add a group below (e.g. "Sauce", "Toppings", "Size").</p>
    </div>
    @endif

    {{-- Existing variant groups --}}
    @foreach($product->variantGroups as $group)
    <div class="border-b border-gray-50" x-data="{ open: true }">
        {{-- Group header --}}
        <div class="flex items-center gap-3 px-6 py-3.5 bg-gray-50/60">
            <button type="button" @click="open = !open"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? '' : '-rotate-90'"></i>
            </button>
            <div class="flex-1 flex items-center gap-3">
                <p class="text-sm font-semibold text-gray-800">{{ $group->name }}</p>
                @php
                    $typeLabel = ['checkbox' => 'Checkbox', 'radio' => 'Single Choice', 'multiselect' => 'Multi-select'][$group->type] ?? $group->type;
                    $typeColor = ['checkbox' => 'bg-blue-50 text-blue-700', 'radio' => 'bg-violet-50 text-violet-700', 'multiselect' => 'bg-teal-50 text-teal-700'][$group->type] ?? 'bg-gray-100 text-gray-600';
                @endphp
                <span class="text-[11px] font-semibold {{ $typeColor }} px-2 py-0.5 rounded-full">{{ $typeLabel }}</span>
                <span class="text-xs text-gray-400">+RM {{ number_format($group->price_modifier, 2) }} per selection</span>
                @if($group->required)
                <span class="text-[11px] font-semibold bg-red-50 text-red-600 px-2 py-0.5 rounded-full">Required</span>
                @endif
            </div>
            <form method="POST" action="{{ route('products.variant-groups.destroy', $group) }}"
                  onsubmit="return confirm('Delete group \'{{ addslashes($group->name) }}\' and all its options?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="text-gray-300 hover:text-red-500 transition-colors px-2 py-1 rounded-lg hover:bg-red-50">
                    <i class="fas fa-trash text-xs"></i>
                </button>
            </form>
        </div>

        {{-- Options list + add option form --}}
        <div x-show="open" x-transition class="px-6 py-4">
            @if($group->options->isEmpty())
            <p class="text-xs text-gray-400 italic mb-3">No options yet — add one below.</p>
            @else
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($group->options as $option)
                <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-sm">
                    <span class="font-medium text-gray-700">{{ $option->name }}</span>
                    @if($option->extra_price > 0)
                    <span class="text-xs text-indigo-500 font-semibold">+RM{{ number_format($option->extra_price, 2) }}</span>
                    @endif
                    <form method="POST" action="{{ route('products.variant-options.destroy', $option) }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="text-gray-300 hover:text-red-500 transition-colors ml-1"
                                onclick="return confirm('Delete option \'{{ addslashes($option->name) }}\'?')">
                            <i class="fas fa-xmark text-xs"></i>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Add option form --}}
            <form method="POST" action="{{ route('products.variant-options.store', $group) }}"
                  class="flex items-end gap-3 pt-3 border-t border-gray-100">
                @csrf
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Option Name</label>
                    <input type="text" name="name" placeholder="e.g. Caramel" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                <div class="w-32">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Extra Price (RM)</label>
                    <input type="number" name="extra_price" value="0" step="0.10" min="0"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-gray-900 hover:bg-gray-700 text-white text-xs font-semibold rounded-lg transition-colors whitespace-nowrap">
                    Add Option
                </button>
            </form>
        </div>
    </div>
    @endforeach

    {{-- ── Add Variant Group form ─────────────────────────────────────────── --}}
    <div class="px-6 py-5 bg-gray-50/40" x-data="{ open: {{ $product->variantGroups->isEmpty() ? 'true' : 'false' }} }">
        <button type="button" @click="open = !open"
                class="flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
            <i class="fas fa-plus-circle"></i>
            <span x-text="open ? 'Hide form' : 'Add Variant Group'"></span>
        </button>

        <div x-show="open" x-transition class="mt-4">
            <form method="POST" action="{{ route('products.variant-groups.store', $product) }}"
                  class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Group Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" placeholder="e.g. Sauce, Toppings, Size" required
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Selection Type <span class="text-red-400">*</span></label>
                    <select name="type" required
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
                        <option value="radio">Single Choice (pick one)</option>
                        <option value="multiselect">Multi-select (pick many)</option>
                        <option value="checkbox">Checkbox (yes / no)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Price per Selection (RM) <span class="text-red-400">*</span></label>
                    <input type="number" name="price_modifier" value="0" step="0.50" min="0" required
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
                </div>
                <div class="flex items-end gap-3">
                    <label class="flex items-center gap-2 mb-2.5 cursor-pointer whitespace-nowrap">
                        <input type="hidden" name="required" value="0">
                        <input type="checkbox" name="required" value="1"
                               class="w-4 h-4 text-indigo-600 rounded border-gray-300">
                        <span class="text-xs font-medium text-gray-600">Required</span>
                    </label>
                    <button type="submit"
                            class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                        Add Group
                    </button>
                </div>
            </form>

            {{-- Type reference --}}
            <div class="grid grid-cols-3 gap-3 mt-4">
                <div class="bg-blue-50 rounded-xl p-3 text-xs text-blue-700">
                    <p class="font-bold mb-0.5">Checkbox</p>
                    <p class="text-blue-500">On/off choice. Good for single add-ons like "Add Ice Cream".</p>
                </div>
                <div class="bg-violet-50 rounded-xl p-3 text-xs text-violet-700">
                    <p class="font-bold mb-0.5">Single Choice</p>
                    <p class="text-violet-500">Pick exactly one from a list. Good for "Sauce" or "Size".</p>
                </div>
                <div class="bg-teal-50 rounded-xl p-3 text-xs text-teal-700">
                    <p class="font-bold mb-0.5">Multi-select</p>
                    <p class="text-teal-500">Pick one or more. Good for "Toppings".</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
