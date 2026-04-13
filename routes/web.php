<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

// ── Root — redirect to dashboard (or login if not authenticated) ───────────
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ── All app routes require login ───────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // ── Dashboard ────────────────────────────────────────────────────────────
    Route::get('/dashboard', function () {
        $today     = today();
        $yesterday = today()->subDay();

        $todaySales     = (float) \App\Models\Order::whereDate('created_at', $today)
                            ->where('status', 'completed')->sum('total');
        $yesterdaySales = (float) \App\Models\Order::whereDate('created_at', $yesterday)
                            ->where('status', 'completed')->sum('total');
        $salesChange    = $yesterdaySales > 0
                            ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1)
                            : null;

        $todayOrders     = \App\Models\Order::whereDate('created_at', $today)->count();
        $yesterdayOrders = \App\Models\Order::whereDate('created_at', $yesterday)->count();
        $ordersChange    = $yesterdayOrders > 0
                            ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100, 1)
                            : null;

        $pendingOrders = \App\Models\Order::whereDate('created_at', $today)
                            ->where('status', 'pending')->count();

        $totalProducts = \App\Models\Product::count();
        $newThisWeek   = \App\Models\Product::where('created_at', '>=', now()->startOfWeek())->count();

        $recentOrders = \App\Models\Order::with('items')
                            ->whereDate('created_at', $today)
                            ->latest()
                            ->take(5)
                            ->get();

        $topSelling = \App\Models\OrderItem::selectRaw('product_name, SUM(quantity) as total_sold')
                            ->groupBy('product_name')
                            ->orderByDesc('total_sold')
                            ->take(5)
                            ->get();
        $maxSold = $topSelling->max('total_sold') ?: 1;

        return view('dashboard', compact(
            'todaySales', 'yesterdaySales', 'salesChange',
            'todayOrders', 'ordersChange',
            'pendingOrders',
            'totalProducts', 'newThisWeek',
            'recentOrders',
            'topSelling', 'maxSold'
        ));
    })->name('dashboard');

    // ── Products (resource + variant sub-routes) ──────────────────────────
    Route::resource('products', ProductController::class);

    Route::post('/products/{product}/variant-groups',
        [ProductController::class, 'storeVariantGroup']
    )->name('products.variant-groups.store');

    Route::delete('/products/variant-groups/{variantGroup}',
        [ProductController::class, 'destroyVariantGroup']
    )->name('products.variant-groups.destroy');

    Route::post('/products/variant-groups/{variantGroup}/options',
        [ProductController::class, 'storeVariantOption']
    )->name('products.variant-options.store');

    Route::delete('/products/variant-options/{variantOption}',
        [ProductController::class, 'destroyVariantOption']
    )->name('products.variant-options.destroy');

    // ── POS ───────────────────────────────────────────────────────────────
    Route::get('/pos', function () {
        $products = \App\Models\Product::with('variantGroups.options')
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'id'             => $p->id,
                'name'           => $p->name,
                'price'          => (float) $p->price,
                'category'       => $p->category,
                'isAvailable'    => $p->is_available,
                'isCustomizable' => $p->is_customizable,
                'image'          => $p->image ?? '',
                'variantGroups'  => $p->variantGroups->map(fn ($g) => [
                    'id'            => $g->id,
                    'name'          => $g->name,
                    'type'          => $g->type,
                    'priceModifier' => (float) $g->price_modifier,
                    'required'      => $g->required,
                    'options'       => $g->options->map(fn ($o) => [
                        'id'         => $o->id,
                        'name'       => $o->name,
                        'extraPrice' => (float) $o->extra_price,
                    ])->values(),
                ])->values(),
            ]);

        $todayCompletedOrders = \App\Models\Order::whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();

        return view('pos', compact('products', 'todayCompletedOrders'));
    })->name('pos');

    // ── Orders ────────────────────────────────────────────────────────────
    Route::get('/orders',                  [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders',                 [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    // ── Profile (Breeze) ──────────────────────────────────────────────────
    Route::get('/profile',    [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

});

// ── Legacy redirect ────────────────────────────────────────────────────────
Route::permanentRedirect('/orders-legacy', '/orders');

require __DIR__.'/auth.php';
