@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Welcome back, here\'s what\'s happening today.')

@section('content')

{{-- ── Stat Cards ───────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

    {{-- Today's Sales --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-start justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Today's Sales</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">
                RM {{ number_format($todaySales, 2) }}
            </p>
            <div class="flex items-center gap-1.5 mt-2">
                @if($salesChange !== null)
                    @if($salesChange >= 0)
                        <span class="flex items-center gap-0.5 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <i class="fas fa-arrow-up text-[10px]"></i> {{ $salesChange }}%
                        </span>
                    @else
                        <span class="flex items-center gap-0.5 text-xs font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">
                            <i class="fas fa-arrow-down text-[10px]"></i> {{ abs($salesChange) }}%
                        </span>
                    @endif
                    <span class="text-xs text-gray-400">vs yesterday</span>
                @else
                    <span class="text-xs text-gray-400">No data yesterday</span>
                @endif
            </div>
        </div>
        <div class="w-11 h-11 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0">
            <i class="fas fa-money-bill-wave text-emerald-600 text-lg"></i>
        </div>
    </div>

    {{-- Orders Today --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-start justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Orders Today</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $todayOrders }}</p>
            <div class="flex items-center gap-1.5 mt-2">
                @if($ordersChange !== null)
                    @if($ordersChange >= 0)
                        <span class="flex items-center gap-0.5 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <i class="fas fa-arrow-up text-[10px]"></i> {{ $ordersChange }}%
                        </span>
                    @else
                        <span class="flex items-center gap-0.5 text-xs font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">
                            <i class="fas fa-arrow-down text-[10px]"></i> {{ abs($ordersChange) }}%
                        </span>
                    @endif
                    <span class="text-xs text-gray-400">vs yesterday</span>
                @else
                    <span class="text-xs text-gray-400">No data yesterday</span>
                @endif
            </div>
        </div>
        <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
            <i class="fas fa-shopping-bag text-blue-600 text-lg"></i>
        </div>
    </div>

    {{-- Pending Orders --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-start justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending Orders</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $pendingOrders }}</p>
            <div class="flex items-center gap-1.5 mt-2">
                @if($pendingOrders > 0)
                    <span class="flex items-center gap-0.5 text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                        <i class="fas fa-clock text-[10px]"></i> Needs action
                    </span>
                @else
                    <span class="flex items-center gap-0.5 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                        <i class="fas fa-check text-[10px]"></i> All clear
                    </span>
                @endif
            </div>
        </div>
        <div class="w-11 h-11 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
            <i class="fas fa-clock text-amber-600 text-lg"></i>
        </div>
    </div>

    {{-- Total Products --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-start justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Products</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalProducts }}</p>
            <div class="flex items-center gap-1.5 mt-2">
                @if($newThisWeek > 0)
                    <span class="flex items-center gap-0.5 text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">
                        <i class="fas fa-plus text-[10px]"></i> {{ $newThisWeek }} new this week
                    </span>
                @else
                    <span class="text-xs text-gray-400">In catalog</span>
                @endif
            </div>
        </div>
        <div class="w-11 h-11 bg-violet-100 rounded-xl flex items-center justify-center shrink-0">
            <i class="fas fa-box-open text-violet-600 text-lg"></i>
        </div>
    </div>
</div>

{{-- ── Daily Target Banner ─────────────────────────────────────────────── --}}
<div class="bg-linear-to-r from-indigo-600 via-violet-600 to-indigo-700 rounded-2xl p-5 shadow-lg mb-6"
     x-data="{
         target: 0,
         newTarget: '',
         editing: false,
         todayOrders: {{ $todayOrders }},
         get progress() { return this.target > 0 ? Math.min(100, Math.round((this.todayOrders / this.target) * 100)) : 0; },
         get tier() {
             const p = this.progress;
             if (p >= 100) return 4;
             if (p >= 75)  return 3;
             if (p >= 50)  return 2;
             if (p >= 25)  return 1;
             return 0;
         },
         get milestone() {
             if (!this.target) return 'Set a target and chase it every day.';
             const msgs = [
                 'Every order counts — let\'s get started!',
                 'Good start — keep the orders coming!',
                 'Halfway there, you\'re on fire!',
                 'Almost there — one final push!',
                 '🎉 Target crushed! Celebrate the win!',
             ];
             return msgs[this.tier];
         },
         init() {
             this.target = Number(localStorage.getItem('daily-order-target') || 0);
             this.newTarget = this.target || '';
         },
         save() {
             const val = parseInt(this.newTarget);
             if (val > 0) {
                 this.target = val;
                 localStorage.setItem('daily-order-target', String(val));
             }
             this.editing = false;
         }
     }"
     x-init="init()">
    <div class="flex flex-col sm:flex-row sm:items-center gap-5">

        {{-- Left: info + bar --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <i class="fas fa-bullseye text-white/70 text-sm"></i>
                <p class="text-xs font-bold text-white/60 uppercase tracking-wider">Daily Order Target</p>
            </div>
            <p class="text-2xl font-bold text-white"
               x-text="target ? `${todayOrders} / ${target} orders` : '— / — orders'"></p>
            <p class="text-sm text-white/60 mt-0.5" x-text="milestone"></p>

            {{-- Progress --}}
            <div class="mt-3 h-2.5 rounded-full overflow-hidden relative" style="background: rgba(255,255,255,0.2)">
                <div class="absolute inset-y-0 left-1/4 w-px z-10" style="background: rgba(255,255,255,0.3)"></div>
                <div class="absolute inset-y-0 left-1/2 w-px z-10" style="background: rgba(255,255,255,0.3)"></div>
                <div class="absolute inset-y-0 left-3/4 w-px z-10" style="background: rgba(255,255,255,0.3)"></div>
                <div class="h-full rounded-full transition-all duration-700"
                     :style="`width: ${progress}%; background: rgba(255,255,255,0.9)`"></div>
            </div>
            <div class="flex justify-between text-[10px] text-white/30 mt-1">
                <span>0%</span><span>25%</span><span>50%</span><span>75%</span>
                <span x-text="target ? target + ' orders' : '100%'"></span>
            </div>
        </div>

        {{-- Right: set / edit target --}}
        <div class="shrink-0 self-start sm:self-center">
            <template x-if="!editing">
                <button @click="editing = true; newTarget = target || ''"
                        class="flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors whitespace-nowrap">
                    <i class="fas fa-pen text-xs"></i>
                    <span x-text="target ? 'Edit Target' : 'Set Target'"></span>
                </button>
            </template>
            <template x-if="editing">
                <div class="flex items-center gap-2">
                    <input type="number" x-model="newTarget"
                           x-ref="inp"
                           x-effect="editing && $nextTick(() => $refs.inp?.focus())"
                           min="1" max="999" placeholder="e.g. 20"
                           @keydown.enter="save()" @keydown.escape="editing = false"
                           class="w-24 text-sm font-bold text-center bg-white text-indigo-700 border-0 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-white/50">
                    <button @click="save()"
                            class="bg-white text-indigo-700 text-sm font-bold px-4 py-2.5 rounded-xl hover:bg-indigo-50 transition-colors">
                        Save
                    </button>
                    <button @click="editing = false"
                            class="text-white/60 hover:text-white text-sm px-1 py-2.5 transition-colors">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>

{{-- ── Lower Section ────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- Recent Orders --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Recent Orders</h3>
                <p class="text-xs text-gray-400 mt-0.5">Latest transactions today</p>
            </div>
            <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                View all <i class="fas fa-arrow-right text-[10px] ml-0.5"></i>
            </a>
        </div>

        @if($recentOrders->isEmpty())
        <div class="flex flex-col items-center justify-center py-14 text-center">
            <i class="fas fa-receipt text-3xl text-gray-200 mb-3"></i>
            <p class="text-sm text-gray-400 font-medium">No orders yet today</p>
            <a href="{{ route('pos') }}" class="mt-3 text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                Go to POS <i class="fas fa-arrow-right text-[10px] ml-0.5"></i>
            </a>
        </div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($recentOrders as $order)
            <div class="flex items-center justify-between px-6 py-3.5 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3.5">
                    <div class="w-9 h-9 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                        <i class="fas fa-receipt text-indigo-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $order->items->sum('quantity') }} {{ $order->items->sum('quantity') === 1 ? 'item' : 'items' }}
                            · {{ $order->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <p class="text-sm font-bold text-gray-800">RM {{ number_format($order->total, 2) }}</p>
                    @if($order->status === 'completed')
                        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">Completed</span>
                    @elseif($order->status === 'pending')
                        <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full">Pending</span>
                    @else
                        <span class="text-xs font-semibold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full">Cancelled</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Right column --}}
    <div class="flex flex-col gap-5">

        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('pos') }}"
                   class="flex flex-col items-center gap-2 p-4 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors group">
                    <div class="w-9 h-9 bg-indigo-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-cash-register text-white text-sm"></i>
                    </div>
                    <span class="text-xs font-semibold text-indigo-700">New Sale</span>
                </a>
                <a href="{{ route('products.create') }}"
                   class="flex flex-col items-center gap-2 p-4 bg-violet-50 hover:bg-violet-100 rounded-xl transition-colors group">
                    <div class="w-9 h-9 bg-violet-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-plus text-white text-sm"></i>
                    </div>
                    <span class="text-xs font-semibold text-violet-700">Add Product</span>
                </a>
                <a href="{{ route('orders.index') }}"
                   class="flex flex-col items-center gap-2 p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors group">
                    <div class="w-9 h-9 bg-blue-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-list text-white text-sm"></i>
                    </div>
                    <span class="text-xs font-semibold text-blue-700">View Orders</span>
                </a>
                <a href="{{ route('products.index') }}"
                   class="flex flex-col items-center gap-2 p-4 bg-amber-50 hover:bg-amber-100 rounded-xl transition-colors group">
                    <div class="w-9 h-9 bg-amber-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-boxes-stacked text-white text-sm"></i>
                    </div>
                    <span class="text-xs font-semibold text-amber-700">Products</span>
                </a>
            </div>
        </div>

        {{-- Top Selling --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex-1">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Top Selling</h3>

            @if($topSelling->isEmpty())
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <i class="fas fa-chart-bar text-2xl text-gray-200 mb-2"></i>
                <p class="text-xs text-gray-400">No sales data yet</p>
            </div>
            @else
            @php
                $barColors = ['bg-indigo-500','bg-violet-500','bg-blue-500','bg-emerald-500','bg-amber-500'];
            @endphp
            <div class="space-y-3">
                @foreach($topSelling as $i => $item)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium text-gray-700 truncate max-w-30">{{ $item->product_name }}</span>
                        <span class="text-xs text-gray-400 shrink-0 ml-2">{{ $item->total_sold }} sold</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="{{ $barColors[$i % count($barColors)] }} h-full rounded-full transition-all duration-500"
                             style="width: {{ round(($item->total_sold / $maxSold) * 100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>

@endsection
