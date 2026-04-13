<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with('items')->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } else {
            $query->whereDate('created_at', today());
        }

        $orders = $query->get();

        $stats = [
            'total'     => Order::whereDate('created_at', today())->count(),
            'sales'     => Order::whereDate('created_at', today())->where('status', 'completed')->sum('total'),
            'completed' => Order::whereDate('created_at', today())->where('status', 'completed')->count(),
            'pending'   => Order::whereDate('created_at', today())->where('status', 'pending')->count(),
            'cancelled' => Order::whereDate('created_at', today())->where('status', 'cancelled')->count(),
        ];

        return view('orders', compact('orders', 'stats'));
    }

    /**
     * Called from POS via fetch() — accepts JSON cart and saves to DB.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cart'           => 'required|array|min:1',
            'cart.*.id'      => 'nullable|integer',
            'cart.*.name'    => 'required|string',
            'cart.*.price'   => 'required|numeric|min:0',
            'cart.*.quantity'=> 'required|integer|min:1',
            'cart.*.customizations' => 'nullable|array',
            'payment_method' => 'required|in:cash,qr',
        ]);

        $subtotal = collect($data['cart'])
            ->sum(fn ($item) => $item['price'] * $item['quantity']);

        $order = Order::create([
            'subtotal'       => $subtotal,
            'total'          => $subtotal,
            'payment_method' => $data['payment_method'],
            'status'         => 'pending',
        ]);

        foreach ($data['cart'] as $item) {
            $order->items()->create([
                'product_id'     => $item['id'] ?? null,
                'product_name'   => $item['name'],
                'unit_price'     => $item['price'],
                'quantity'       => $item['quantity'],
                'subtotal'       => $item['price'] * $item['quantity'],
                'customizations' => $item['customizations'] ?? [],
            ]);
        }

        return response()->json([
            'success'      => true,
            'order_number' => $order->order_number,
            'total'        => number_format($order->total, 2),
        ]);
    }

    /**
     * Inline status update — called from the Orders page.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', "Order {$order->order_number} marked as {$request->status}.");
    }
}
