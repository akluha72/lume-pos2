@extends('layouts.app')

@section('title', 'Orders')
@section('subtitle', 'Today\'s transaction history')

@section('content')

@php
$statusConfig = [
    'completed' => ['label' => 'Completed', 'pill' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'dot' => 'bg-emerald-500'],
    'pending'   => ['label' => 'Pending',   'pill' => 'bg-amber-50 text-amber-700 ring-amber-200',     'dot' => 'bg-amber-400'],
    'cancelled' => ['label' => 'Cancelled', 'pill' => 'bg-rose-50 text-rose-600 ring-rose-200',        'dot' => 'bg-rose-400'],
];
@endphp

{{-- Flash --}}
@if(session('success'))
<div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium px-4 py-3 rounded-xl mb-5"
     x-data x-init="setTimeout(() => $el.remove(), 4000)">
    <i class="fas fa-circle-check text-emerald-500"></i> {{ session('success') }}
</div>
@endif

{{-- ── Daily Target Progress ─────────────────────────────────────────────── --}}
<div class="bg-linear-to-r from-indigo-600 via-violet-600 to-indigo-700 rounded-2xl p-5 shadow-lg mb-6"
     x-data="{
         target: 0,
         completed: {{ $stats['completed'] }},
         get progress() { return this.target > 0 ? Math.min(100, Math.round((this.completed / this.target) * 100)) : 0; },
         get tier() {
             const p = this.progress;
             if (p >= 100) return 4;
             if (p >= 75)  return 3;
             if (p >= 50)  return 2;
             if (p >= 25)  return 1;
             return 0;
         },
         get milestone() {
             if (!this.target) return 'Set a target on the dashboard to track daily progress.';
             const msgs = [
                 'Every completed order moves the bar!',
                 'Good start — keep the orders coming!',
                 'Halfway there, you\'re on fire!',
                 'Almost there — one final push!',
                 '🎉 Target crushed! Amazing work today!',
             ];
             return msgs[this.tier];
         }
     }"
     x-init="target = Number(localStorage.getItem('daily-order-target') || 0)">
    <div class="flex flex-col sm:flex-row sm:items-center gap-5">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <i class="fas fa-bullseye text-white/70 text-sm"></i>
                <p class="text-xs font-bold text-white/60 uppercase tracking-wider">Daily Order Target</p>
            </div>
            <p class="text-2xl font-bold text-white"
               x-text="target ? `${completed} / ${target} orders` : '— / — orders'"></p>
            <p class="text-sm text-white/60 mt-0.5" x-text="milestone"></p>

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

        <div class="shrink-0 self-start sm:self-center text-right">
            <p class="text-3xl font-black text-white tabular-nums" x-text="target ? progress + '%' : '—'"></p>
            <p class="text-xs text-white/50 mt-0.5">of goal reached</p>
        </div>
    </div>
</div>

{{-- ── Summary Cards ─────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Orders</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Today</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Sales</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">RM {{ number_format($stats['sales'], 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">Completed only</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Completed</p>
        <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $stats['completed'] }}</p>
        @if($stats['total'] > 0)
        <p class="text-xs text-gray-400 mt-1">{{ round($stats['completed'] / $stats['total'] * 100) }}% of orders</p>
        @endif
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending</p>
        <p class="text-3xl font-bold text-amber-500 mt-1">{{ $stats['pending'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Awaiting confirmation</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Cancelled</p>
        <p class="text-3xl font-bold text-rose-500 mt-1">{{ $stats['cancelled'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Voided today</p>
    </div>
</div>

{{-- ── Filter Bar ────────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('orders.index') }}"
      class="flex flex-wrap items-center gap-3 mb-5">
    <div class="flex gap-1 bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
        <button type="submit" name="status" value="{{ $val }}"
                class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors
                       {{ (request('status', 'all') === $val) ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>
    <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-3 py-2 shadow-sm">
        <i class="fas fa-calendar-day text-gray-400 text-sm"></i>
        <input type="date" name="date" value="{{ request('date', today()->toDateString()) }}"
               onchange="this.form.submit()"
               class="text-sm text-gray-700 bg-transparent focus:outline-none">
    </div>
    @if(request()->hasAny(['status','date']))
    <a href="{{ route('orders.index') }}"
       class="text-xs text-gray-400 hover:text-gray-600 flex items-center gap-1">
        <i class="fas fa-xmark"></i> Reset
    </a>
    @endif
    <span class="ml-auto text-xs text-gray-400">{{ $orders->count() }} order(s) shown</span>
</form>

{{-- ── Orders Table ──────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">

    {{-- Column headings --}}
    <div class="grid grid-cols-12 gap-4 px-6 py-3 bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase tracking-wider rounded-t-2xl overflow-hidden">
        <div class="col-span-1"></div>
        <div class="col-span-2">Order</div>
        <div class="col-span-3">Items</div>
        <div class="col-span-2 text-center">Payment</div>
        <div class="col-span-2 text-right">Total</div>
        <div class="col-span-2 text-right">Status / Action</div>
    </div>

    @forelse($orders as $order)
    @php $cfg = $statusConfig[$order->status]; @endphp

    <div class="border-b border-gray-50 last:border-0" x-data="{ open: false }">

        {{-- Main row --}}
        <div class="grid grid-cols-12 gap-4 px-6 py-4 hover:bg-gray-50/60 transition-colors items-center">

            {{-- Expand toggle --}}
            <div class="col-span-1">
                <button type="button" @click="open = !open"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-indigo-600 transition-colors">
                    <i class="fas text-xs transition-transform duration-200"
                       :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                </button>
            </div>

            {{-- Order # + time --}}
            <div class="col-span-2">
                <p class="text-sm font-bold text-gray-800">{{ $order->order_number }}</p>
                <p class="text-xs text-gray-400">{{ $order->created_at->format('h:i A') }}</p>
            </div>

            {{-- Item preview --}}
            <div class="col-span-3">
                <div class="flex flex-wrap gap-1">
                    @foreach($order->items->take(2) as $item)
                    <span class="text-xs bg-gray-100 text-gray-600 font-medium px-2 py-0.5 rounded-md">
                        {{ $item->quantity > 1 ? $item->quantity.'× ' : '' }}{{ $item->product_name }}
                    </span>
                    @endforeach
                    @if($order->items->count() > 2)
                    <span class="text-xs bg-gray-100 text-gray-400 px-2 py-0.5 rounded-md cursor-pointer hover:bg-indigo-50 hover:text-indigo-600"
                          @click="open = true">
                        +{{ $order->items->count() - 2 }} more
                    </span>
                    @endif
                </div>
                <p class="text-xs text-gray-400 mt-0.5">{{ $order->items->sum('quantity') }} {{ $order->items->sum('quantity') === 1 ? 'item' : 'items' }}</p>
            </div>

            {{-- Payment method --}}
            <div class="col-span-2 flex justify-center">
                @if($order->payment_method === 'qr')
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-violet-700 bg-violet-50 px-2.5 py-1 rounded-full">
                    <i class="fas fa-qrcode text-[10px]"></i> DuitNow
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full">
                    <i class="fas fa-money-bill-wave text-[10px]"></i> Cash
                </span>
                @endif
            </div>

            {{-- Total --}}
            <div class="col-span-2 text-right">
                <p class="text-sm font-bold text-gray-900">RM {{ number_format($order->total, 2) }}</p>
            </div>

            {{-- Status + action buttons --}}
            <div class="col-span-2 flex items-center justify-end gap-2">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full ring-1 {{ $cfg['pill'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }}"></span>
                    {{ $cfg['label'] }}
                </span>

                {{-- Action dropdown --}}
                @if($order->status !== 'cancelled')
                <div class="relative" x-data="{ menu: false }" @click.outside="menu = false">
                    <button type="button" @click="menu = !menu"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors">
                        <i class="fas fa-ellipsis-vertical text-xs"></i>
                    </button>
                    <div x-show="menu" x-transition
                         class="absolute right-0 top-9 z-20 w-44 bg-white rounded-xl border border-gray-200 shadow-lg py-1 text-sm">
                        @if($order->status !== 'completed')
                        <form method="POST" action="{{ route('orders.status', $order) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 hover:bg-emerald-50 text-emerald-700 font-medium flex items-center gap-2">
                                <i class="fas fa-circle-check text-xs"></i> Mark Completed
                            </button>
                        </form>
                        @endif
                        @if($order->status !== 'pending')
                        <form method="POST" action="{{ route('orders.status', $order) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="pending">
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 hover:bg-amber-50 text-amber-700 font-medium flex items-center gap-2">
                                <i class="fas fa-clock text-xs"></i> Mark Pending
                            </button>
                        </form>
                        @endif
                        <div class="border-t border-gray-100 mt-1 pt-1">
                            <form method="POST" action="{{ route('orders.status', $order) }}"
                                  onsubmit="return confirm('Cancel order {{ $order->order_number }}?')">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 hover:bg-rose-50 text-rose-600 font-medium flex items-center gap-2">
                                    <i class="fas fa-ban text-xs"></i> Cancel Order
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Expanded detail panel --}}
        <div x-show="open" x-transition:enter="transition duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-indigo-50/40 border-t border-indigo-100 px-6 py-4">

            <p class="text-xs font-bold text-indigo-700 uppercase tracking-wider mb-3">Order Items</p>
            <div class="space-y-2">
                @foreach($order->items as $item)
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 bg-white rounded-lg border border-indigo-100 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-xs font-bold text-indigo-500">{{ $item->quantity }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $item->product_name }}</p>
                            @if(!empty($item->customizations))
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($item->customizations as $custom)
                                <span class="text-[10px] bg-amber-100 text-amber-700 font-medium px-1.5 py-0.5 rounded-full">{{ $custom }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-gray-800">RM {{ number_format($item->subtotal, 2) }}</p>
                        <p class="text-xs text-gray-400">RM {{ number_format($item->unit_price, 2) }} each</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex justify-between items-center mt-4 pt-3 border-t border-indigo-200">
                <span class="text-xs text-gray-500">Placed at {{ $order->created_at->format('h:i A, d M Y') }}</span>
                <span class="text-sm font-bold text-gray-900">Total: RM {{ number_format($order->total, 2) }}</span>
            </div>
        </div>

    </div>
    @empty
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
            <i class="fas fa-receipt text-gray-300 text-2xl"></i>
        </div>
        <p class="text-sm font-medium text-gray-500">No orders yet</p>
        <p class="text-xs text-gray-400 mt-1">Orders submitted from the POS will appear here.</p>
        <a href="{{ route('pos') }}" class="mt-4 text-xs font-semibold text-indigo-600 hover:text-indigo-700">
            Go to POS <i class="fas fa-arrow-right text-[10px] ml-0.5"></i>
        </a>
    </div>
    @endforelse
</div>

@endsection
