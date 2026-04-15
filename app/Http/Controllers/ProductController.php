<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariantGroup;
use App\Models\ProductVariantOption;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    // ─── Resource Methods ────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('availability')) {
            $query->where('is_available', $request->availability === 'available');
        }

        $products   = $query->orderBy('sort_order')->orderBy('name')->get();
        $categories = Product::distinct()->orderBy('category')->pluck('category');

        return view('products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Product::distinct()->orderBy('category')->pluck('category');
        return view('products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'category'        => 'required|string|max:100',
            'price'           => 'required|numeric|min:0',
            'sort_order'      => 'nullable|integer',
            'image'           => 'nullable|image|max:4096',
            'is_available'    => 'nullable|boolean',
        ]);

        $data['is_available'] = $request->boolean('is_available', true);
        $data['sort_order']   = $data['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/products'), $filename);
            $data['image'] = '/images/products/' . $filename;
        }

        $product = Product::create($data);

        return redirect()->route('products.edit', $product)
            ->with('success', 'Product created! Add variant options below if needed.');
    }

    public function show(Product $product): RedirectResponse
    {
        return redirect()->route('products.edit', $product);
    }

    public function edit(Product $product): View
    {
        $product->load('variantGroups.options');
        $categories = Product::distinct()->orderBy('category')->pluck('category');

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'category'        => 'required|string|max:100',
            'price'           => 'required|numeric|min:0',
            'sort_order'      => 'nullable|integer',
            'image'           => 'nullable|image|max:4096',
            'is_available' => 'nullable|boolean',
        ]);

        $data['is_available'] = $request->boolean('is_available');
        $data['sort_order']   = $data['sort_order'] ?? $product->sort_order;

        if ($request->hasFile('image')) {
            // Remove old image if it was uploaded (not a seeded path)
            if ($product->image && str_starts_with($product->image, '/images/products/')) {
                $old = public_path(ltrim($product->image, '/'));
                if (file_exists($old) && !str_contains($old, 'seeded_')) {
                    @unlink($old);
                }
            }
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/products'), $filename);
            $data['image'] = '/images/products/' . $filename;
        } else {
            unset($data['image']); // don't overwrite with null
        }

        $product->update($data);

        return back()->with('success', 'Product saved.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', '"' . $product->name . '" deleted.');
    }

    // ─── Variant Groups ───────────────────────────────────────────────────────

    public function storeVariantGroup(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'type'           => 'required|in:checkbox,radio,multiselect',
            'price_modifier' => 'required|numeric|min:0',
            'required'       => 'nullable|boolean',
        ]);

        $data['required']    = $request->boolean('required');
        $data['sort_order']  = $product->variantGroups()->count();

        $product->variantGroups()->create($data);

        return back()->with('success', 'Variant group "' . $data['name'] . '" added.');
    }

    public function destroyVariantGroup(ProductVariantGroup $variantGroup): RedirectResponse
    {
        $product = $variantGroup->product;
        $name    = $variantGroup->name;
        $variantGroup->delete();

        return redirect()->route('products.edit', $product)
            ->with('success', 'Variant group "' . $name . '" deleted.');
    }

    // ─── Variant Options ──────────────────────────────────────────────────────

    public function storeVariantOption(Request $request, ProductVariantGroup $variantGroup): RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'extra_price' => 'nullable|numeric|min:0',
        ]);

        $data['extra_price'] = $data['extra_price'] ?? 0;
        $data['sort_order']  = $variantGroup->options()->count();

        $variantGroup->options()->create($data);

        return back()->with('success', 'Option "' . $data['name'] . '" added.');
    }

    public function destroyVariantOption(ProductVariantOption $variantOption): RedirectResponse
    {
        $product = $variantOption->group->product;
        $name    = $variantOption->name;
        $variantOption->delete();

        return redirect()->route('products.edit', $product)
            ->with('success', 'Option "' . $name . '" deleted.');
    }
}
