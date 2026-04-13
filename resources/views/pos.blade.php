@extends('layouts.app')

@section('title', 'Point of Sale')

@section('content')
{{-- Canvas-confetti for target celebration --}}
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script>
function launchDailyTargetConfetti() {
    confetti({ particleCount: 120, spread: 80, origin: { y: 0.35 }, colors: ['#6366f1','#8b5cf6','#10b981','#f59e0b','#06b6d4'] });
    setTimeout(() => confetti({ particleCount: 60, spread: 55, angle: 60,  origin: { y: 0.35, x: 0.1 } }), 350);
    setTimeout(() => confetti({ particleCount: 60, spread: 55, angle: 120, origin: { y: 0.35, x: 0.9 } }), 600);
}
</script>

<div x-data="posSystem()" x-init="init()"
     class="flex overflow-hidden -m-6"
     style="height: calc(100vh - 73px)">

    {{-- ══════════════════════════════════════════════════════════
         LEFT — Product Grid
         ══════════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col bg-slate-50 overflow-hidden">

        {{-- ── Daily Target Progress Bar ───────────────────────────────── --}}
        <div class="bg-white border-b border-gray-100 px-5 py-3 shrink-0">
            <div class="flex items-center gap-4">

                {{-- Label + count --}}
                <div class="shrink-0 min-w-[120px]">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Daily Target</p>
                    <p class="text-sm font-bold text-gray-900 mt-0.5"
                       x-text="dailyTarget ? `${dailyCompleted} / ${dailyTarget}` : '— orders'"></p>
                </div>

                {{-- Progress bar + message --}}
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-medium" :class="tierColor" x-text="statusMessage"></span>
                        <span class="text-xs font-bold text-gray-400 tabular-nums"
                              x-text="dailyTarget ? progressWidth + '%' : ''"></span>
                    </div>
                    <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden relative">
                        {{-- Milestone tick marks --}}
                        <div class="absolute inset-y-0 left-1/4 w-px bg-white/80 z-10"></div>
                        <div class="absolute inset-y-0 left-1/2 w-px bg-white/80 z-10"></div>
                        <div class="absolute inset-y-0 left-3/4 w-px bg-white/80 z-10"></div>
                        {{-- Progress fill — color changes with tier --}}
                        <div class="h-full rounded-full transition-all duration-700"
                             :style="`width: ${progressWidth}%; background: ${barGradient}`"></div>
                    </div>
                    {{-- Milestone labels --}}
                    <div class="flex justify-between text-[9px] text-gray-300 mt-0.5 leading-none">
                        <span>0</span><span>25%</span><span>50%</span><span>75%</span>
                        <span x-text="dailyTarget || '100%'"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search + Category tabs --}}
        <div class="bg-white border-b border-gray-200 px-5 pt-4 pb-0 shrink-0">
            <div class="relative mb-3">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" placeholder="Search menu…"
                       x-model="search"
                       class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
            {{-- Category tabs --}}
            <div class="flex gap-1 overflow-x-auto pb-0 scrollbar-hide">
                <button type="button" @click="activeCategory = 'all'"
                        :class="activeCategory === 'all' ? 'border-b-2 border-indigo-600 text-indigo-700 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                        class="px-3 py-2 text-sm whitespace-nowrap transition-colors">All</button>
                <template x-for="cat in categories" :key="cat">
                    <button type="button" @click="activeCategory = cat"
                            :class="activeCategory === cat ? 'border-b-2 border-indigo-600 text-indigo-700 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                            class="px-3 py-2 text-sm whitespace-nowrap transition-colors" x-text="cat"></button>
                </template>
            </div>
        </div>

        {{-- Product cards --}}
        <div class="flex-1 overflow-y-auto p-5">
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3" x-show="filteredProducts.length > 0">
                <template x-for="product in filteredProducts" :key="product.id">
                    <button type="button"
                            @click="product.isCustomizable ? openCustomizer(product) : addToCart(product)"
                            class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden text-left hover:shadow-md hover:border-indigo-200 transition-all group active:scale-95">
                        <div class="relative">
                            <img :src="product.image || ''" :alt="product.name"
                                 class="w-full h-32 object-cover"
                                 x-show="product.image"
                                 x-on:error="$el.style.display='none'; $el.nextElementSibling.style.display='flex'">
                            <div class="w-full h-32 bg-linear-to-br from-indigo-50 to-violet-100 flex items-center justify-center"
                                 :style="product.image ? 'display:none' : 'display:flex'">
                                <i class="fas fa-image text-indigo-200 text-3xl"></i>
                            </div>
                            <span x-show="product.isCustomizable"
                                  class="absolute top-2 right-2 bg-amber-400 text-amber-900 text-[10px] font-bold px-1.5 py-0.5 rounded-full">CUSTOM</span>
                        </div>
                        <div class="p-3">
                            <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-indigo-700 transition-colors" x-text="product.name"></p>
                            <p class="text-sm font-bold text-indigo-600 mt-0.5" x-text="'RM ' + product.price.toFixed(2)"></p>
                        </div>
                    </button>
                </template>
            </div>
            <div x-show="filteredProducts.length === 0" class="flex flex-col items-center justify-center h-48 text-gray-400">
                <i class="fas fa-magnifying-glass text-3xl mb-2 text-gray-200"></i>
                <p class="text-sm">No products found</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         RIGHT — Cart
         ══════════════════════════════════════════════════════════ --}}
    <div class="w-80 xl:w-96 bg-white border-l border-gray-200 flex flex-col shrink-0">

        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Current Order</h3>
            <p class="text-xs text-gray-400 mt-0.5" x-text="cart.length + ' item(s)'"></p>
        </div>

        {{-- Cart items --}}
        <div class="flex-1 overflow-y-auto px-5 py-3 space-y-2">
            <div x-show="cart.length === 0"
                 class="h-full flex flex-col items-center justify-center text-gray-300 py-16">
                <i class="fas fa-basket-shopping text-5xl mb-3"></i>
                <p class="text-sm font-medium text-gray-400">Cart is empty</p>
                <p class="text-xs text-gray-300 mt-1">Tap a product to add it</p>
            </div>

            <template x-for="(item, index) in cart" :key="index">
                <div class="bg-gray-50 rounded-xl p-3">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-gray-800 leading-snug flex-1" x-text="item.name"></p>
                        <button @click="removeFromCart(index)"
                                class="text-gray-300 hover:text-red-500 transition-colors shrink-0 mt-0.5">
                            <i class="fas fa-xmark text-xs"></i>
                        </button>
                    </div>
                    {{-- Customization badges --}}
                    <template x-if="item.customizations && item.customizations.length">
                        <div class="flex flex-wrap gap-1 mt-1.5">
                            <template x-for="c in item.customizations" :key="c">
                                <span class="text-[10px] bg-amber-100 text-amber-700 font-medium px-1.5 py-0.5 rounded-full" x-text="c"></span>
                            </template>
                        </div>
                    </template>
                    {{-- Qty + line total --}}
                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center gap-2">
                            <button @click="updateQuantity(index, item.quantity - 1)"
                                    class="w-6 h-6 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 transition-colors text-xs font-bold">−</button>
                            <span class="text-sm font-semibold text-gray-800 w-5 text-center" x-text="item.quantity"></span>
                            <button @click="updateQuantity(index, item.quantity + 1)"
                                    class="w-6 h-6 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 transition-colors text-xs font-bold">+</button>
                        </div>
                        <span class="text-sm font-bold text-indigo-600"
                              x-text="'RM ' + (item.price * item.quantity).toFixed(2)"></span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Totals + payment --}}
        <div class="border-t border-gray-100 px-5 py-4 space-y-3">
            <div class="flex justify-between text-sm text-gray-500">
                <span>Subtotal</span>
                <span x-text="'RM ' + subtotal.toFixed(2)"></span>
            </div>
            <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-dashed border-gray-200">
                <span>Total</span>
                <span x-text="'RM ' + total.toFixed(2)"></span>
            </div>

            {{-- Payment method --}}
            <div class="grid grid-cols-2 gap-2 pt-1">
                <button type="button" @click="paymentMethod = 'cash'"
                        :class="paymentMethod === 'cash' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400'"
                        class="py-2.5 text-sm font-semibold border rounded-xl transition-all">
                    <i class="fas fa-money-bill-wave mr-1.5"></i>Cash
                </button>
                <button type="button" @click="paymentMethod = 'qr'"
                        :class="paymentMethod === 'qr' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400'"
                        class="py-2.5 text-sm font-semibold border rounded-xl transition-all">
                    <i class="fas fa-qrcode mr-1.5"></i>DuitNow
                </button>
            </div>

            {{-- Success toast --}}
            <div x-show="saleResult?.success"
                 x-transition:enter="transition duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-center">
                <p class="text-xs font-bold text-emerald-700">
                    <i class="fas fa-circle-check mr-1"></i>
                    Order <span x-text="saleResult?.order_number"></span> saved!
                </p>
                <p class="text-xs text-emerald-500 mt-0.5">RM <span x-text="saleResult?.total"></span></p>
            </div>

            {{-- Error toast --}}
            <div x-show="saleResult?.error"
                 class="bg-rose-50 border border-rose-200 rounded-xl p-3 text-center">
                <p class="text-xs font-semibold text-rose-600" x-text="saleResult?.error"></p>
            </div>

            <button type="button" @click="completeSale()"
                    :disabled="cart.length === 0 || submitting"
                    class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow transition-colors flex items-center justify-center gap-2">
                <svg x-show="submitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span x-text="submitting ? 'Saving…' : 'Submit Order'"></span>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         CUSTOMIZER MODAL — fully dynamic, driven by variantGroups
         ══════════════════════════════════════════════════════════ --}}
    <div x-show="showCustomizer"
         x-transition:enter="transition duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="showCustomizer = false"
         style="display:none">

        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl max-h-[90vh] flex flex-col"
             @click.stop x-transition:enter="transition duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            {{-- Modal header --}}
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100 shrink-0">
                <div>
                    <h2 class="text-lg font-bold text-gray-900" x-text="customizingProduct?.name ?? ''"></h2>
                    <p class="text-sm text-indigo-600 font-semibold mt-0.5"
                       x-text="'Base: RM ' + (customizingProduct?.price ?? 0).toFixed(2)"></p>
                </div>
                <button @click="showCustomizer = false"
                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 transition-colors">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Variant groups (scrollable) --}}
            <div class="overflow-y-auto flex-1 px-6 py-5 space-y-6">
                <template x-if="customizingProduct && customizingProduct.variantGroups.length === 0">
                    <p class="text-sm text-gray-400 text-center py-8">No customizations available for this product.</p>
                </template>

                <template x-for="group in (customizingProduct?.variantGroups ?? [])" :key="group.id">
                    <div>
                        {{-- Group title --}}
                        <div class="flex items-baseline justify-between mb-3">
                            <h3 class="text-sm font-bold text-gray-800" x-text="group.name"></h3>
                            <span class="text-xs text-gray-400"
                                  x-text="group.priceModifier > 0 ? '+RM ' + group.priceModifier.toFixed(2) + ' per selection' : 'Free'"></span>
                        </div>

                        {{-- CHECKBOX --}}
                        <template x-if="group.type === 'checkbox'">
                            <template x-for="option in group.options" :key="option.id">
                                <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all"
                                       :class="customization[group.id] ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="checkbox"
                                           :checked="customization[group.id]"
                                           @change="customization[group.id] = $event.target.checked"
                                           class="w-4 h-4 text-indigo-600 rounded border-gray-300">
                                    <span class="text-sm font-medium text-gray-700" x-text="option.name"></span>
                                    <span class="ml-auto text-xs font-semibold text-indigo-500"
                                          x-show="group.priceModifier + option.extraPrice > 0"
                                          x-text="'+RM ' + (group.priceModifier + option.extraPrice).toFixed(2)"></span>
                                </label>
                            </template>
                        </template>

                        {{-- RADIO (single choice) --}}
                        <template x-if="group.type === 'radio'">
                            <div class="grid grid-cols-2 gap-2">
                                <template x-for="option in group.options" :key="option.id">
                                    <button type="button"
                                            @click="customization[group.id] = (customization[group.id] === option.id ? null : option.id)"
                                            :class="customization[group.id] === option.id ? 'border-indigo-500 bg-indigo-50 text-indigo-700 font-bold' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                                            class="p-3 text-sm text-left rounded-xl border-2 transition-all">
                                        <span x-text="option.name"></span>
                                        <span class="block text-xs font-semibold text-indigo-400 mt-0.5"
                                              x-show="group.priceModifier + option.extraPrice > 0"
                                              x-text="'+RM ' + (group.priceModifier + option.extraPrice).toFixed(2)"></span>
                                    </button>
                                </template>
                            </div>
                        </template>

                        {{-- MULTISELECT --}}
                        <template x-if="group.type === 'multiselect'">
                            <div class="grid grid-cols-2 gap-2">
                                <template x-for="option in group.options" :key="option.id">
                                    <button type="button"
                                            @click="toggleMultiselect(group.id, option.id)"
                                            :class="(customization[group.id] ?? []).includes(option.id) ? 'border-indigo-500 bg-indigo-50 text-indigo-700 font-bold' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                                            class="p-3 text-sm text-left rounded-xl border-2 transition-all">
                                        <span x-text="option.name"></span>
                                        <span class="block text-xs font-semibold text-indigo-400 mt-0.5"
                                              x-show="group.priceModifier + option.extraPrice > 0"
                                              x-text="'+RM ' + (group.priceModifier + option.extraPrice).toFixed(2)"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Modal footer --}}
            <div class="px-6 pb-6 pt-4 border-t border-gray-100 shrink-0">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm text-gray-500">Total for this item</span>
                    <span class="text-lg font-bold text-indigo-600"
                          x-text="'RM ' + customizerTotal().toFixed(2)"></span>
                </div>
                <div class="flex gap-3">
                    <button @click="showCustomizer = false"
                            class="flex-1 py-3 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button @click="addCustomizedProduct()"
                            class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-colors shadow">
                        Add to Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function posSystem() {
    return {
        products: @json($products),
        cart: [],
        paymentMethod: 'cash',
        search: '',
        activeCategory: 'all',
        showCustomizer: false,
        customizingProduct: null,
        customization: {},
        dailyTarget: 0,
        dailyCompleted: {{ $todayCompletedOrders }},
        progressWidth: 0,
        reachedTarget: false,
        confettiActive: false,

        // ── Computed ──────────────────────────────────────────────────────────

        get categories() {
            return [...new Set(this.products.map(p => p.category))];
        },

        get filteredProducts() {
            return this.products.filter(p => {
                const matchSearch   = p.name.toLowerCase().includes(this.search.toLowerCase());
                const matchCategory = this.activeCategory === 'all' || p.category === this.activeCategory;
                return matchSearch && matchCategory;
            });
        },

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
        },

        get total() {
            return this.subtotal;
        },

        // ── Gamification helpers ──────────────────────────────────────────────

        get progressTier() {
            const p = this.progressWidth;
            if (p >= 100) return 4;
            if (p >= 75)  return 3;
            if (p >= 50)  return 2;
            if (p >= 25)  return 1;
            return 0;
        },

        get barGradient() {
            const gradients = [
                'linear-gradient(90deg, #94a3b8, #94a3b8)',                    // 0-24%  slate
                'linear-gradient(90deg, #6366f1, #818cf8)',                    // 25-49% indigo
                'linear-gradient(90deg, #f59e0b, #fbbf24)',                    // 50-74% amber
                'linear-gradient(90deg, #f97316, #fb923c)',                    // 75-99% orange
                'linear-gradient(90deg, #6366f1, #06b6d4, #10b981)',           // 100%   rainbow
            ];
            return gradients[this.progressTier];
        },

        get tierColor() {
            const colors = ['text-gray-400', 'text-indigo-600', 'text-amber-600', 'text-orange-600', 'text-emerald-600'];
            return colors[this.progressTier];
        },

        get statusMessage() {
            if (!this.dailyTarget) return 'Set a target on the dashboard';
            const msgs = [
                "Let's go — every order counts!",
                'Good start, keep pushing!',
                'Halfway — you\'re on fire!',
                'Almost there, one final push!',
                '🎉 Target crushed! Amazing work!',
            ];
            return msgs[this.progressTier];
        },

        // ── Cart ──────────────────────────────────────────────────────────────

        addToCart(product) {
            const existing = this.cart.find(i => i.id === product.id && !i.customizations?.length);
            if (existing) {
                existing.quantity++;
            } else {
                this.cart.push({ ...product, quantity: 1, customizations: [] });
            }
        },

        updateQuantity(index, qty) {
            if (qty <= 0) {
                this.cart.splice(index, 1);
            } else {
                this.cart[index].quantity = qty;
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        // ── Lifecycle ─────────────────────────────────────────────────────────

        init() {
            this.dailyTarget = Number(localStorage.getItem('daily-order-target') || 0);
            this.updateProgress();
        },

        updateProgress() {
            this.progressWidth = this.dailyTarget > 0
                ? Math.min(100, Math.round((this.dailyCompleted / this.dailyTarget) * 100))
                : 0;
            this.reachedTarget = this.dailyTarget > 0 && this.dailyCompleted >= this.dailyTarget;
            if (this.reachedTarget && !this.confettiActive) {
                this.confettiActive = true;
                if (typeof launchDailyTargetConfetti === 'function') launchDailyTargetConfetti();
                setTimeout(() => this.confettiActive = false, 4000);
            }
        },

        incrementDailyProgress() {
            this.dailyCompleted += 1;
            this.updateProgress();
        },

        submitting: false,
        saleResult: null,

        async completeSale() {
            if (!this.cart.length || this.submitting) return;
            this.submitting = true;
            this.saleResult = null;

            try {
                const res = await fetch('/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                                     ?? document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1]
                                                       .replace(/%3D/g, '=') ?? '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        cart: this.cart.map(i => ({
                            id:             i.id,
                            name:           i.name,
                            price:          i.price,
                            quantity:       i.quantity,
                            customizations: i.customizations ?? [],
                        })),
                        payment_method: this.paymentMethod,
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    this.saleResult = { success: true, order_number: data.order_number, total: data.total };
                    this.cart = [];
                    this.paymentMethod = 'cash';
                    this.incrementDailyProgress();
                    setTimeout(() => this.saleResult = null, 5000);
                } else {
                    this.saleResult = { error: data.message ?? 'Something went wrong.' };
                }
            } catch (e) {
                this.saleResult = { error: 'Network error. Please try again.' };
            } finally {
                this.submitting = false;
            }
        },

        // ── Customizer ────────────────────────────────────────────────────────

        openCustomizer(product) {
            this.customizingProduct = product;
            this.customization = {};
            for (const group of product.variantGroups) {
                if (group.type === 'checkbox')    this.customization[group.id] = false;
                else if (group.type === 'radio')  this.customization[group.id] = null;
                else                               this.customization[group.id] = [];
            }
            this.showCustomizer = true;
        },

        toggleMultiselect(groupId, optionId) {
            const arr = this.customization[groupId] ?? [];
            const idx = arr.indexOf(optionId);
            if (idx === -1) arr.push(optionId);
            else arr.splice(idx, 1);
            this.customization[groupId] = [...arr];
        },

        customizerTotal() {
            if (!this.customizingProduct) return 0;
            let total = this.customizingProduct.price;

            for (const group of this.customizingProduct.variantGroups) {
                const sel = this.customization[group.id];
                if (group.type === 'checkbox' && sel) {
                    total += group.priceModifier;
                } else if (group.type === 'radio' && sel !== null) {
                    const opt = group.options.find(o => o.id === sel);
                    if (opt) total += group.priceModifier + opt.extraPrice;
                } else if (group.type === 'multiselect' && sel?.length) {
                    for (const optId of sel) {
                        const opt = group.options.find(o => o.id === optId);
                        if (opt) total += group.priceModifier + opt.extraPrice;
                    }
                }
            }
            return total;
        },

        addCustomizedProduct() {
            if (!this.customizingProduct) return;
            const labels = [];

            for (const group of this.customizingProduct.variantGroups) {
                const sel = this.customization[group.id];
                if (group.type === 'checkbox' && sel) {
                    labels.push(group.options[0]?.name ?? group.name);
                } else if (group.type === 'radio' && sel !== null) {
                    const opt = group.options.find(o => o.id === sel);
                    if (opt) labels.push(opt.name);
                } else if (group.type === 'multiselect' && sel?.length) {
                    for (const optId of sel) {
                        const opt = group.options.find(o => o.id === optId);
                        if (opt) labels.push(opt.name);
                    }
                }
            }

            this.cart.push({
                ...this.customizingProduct,
                price: this.customizerTotal(),
                quantity: 1,
                customizations: labels,
            });

            this.showCustomizer = false;
        },
    };
}
</script>
@endsection
