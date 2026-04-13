<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariantGroup;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // ── Regular (non-customizable) products ──────────────────────────────
        $products = [
            ['name' => 'Classic Butterscotch', 'price' => 9.90,  'category' => 'Pastry', 'image' => '/images/products/classic_butterscotch.jpeg'],
            ['name' => 'Choco Addiction',       'price' => 12.00, 'category' => 'Pastry', 'image' => '/images/products/choco_addiction.jpeg'],
            ['name' => 'Berries Symphony',      'price' => 18.00, 'category' => 'Pastry', 'image' => '/images/products/berries_symphony.jpeg'],
            ['name' => 'Biscoff Fantasy',       'price' => 15.00, 'category' => 'Pastry', 'image' => '/images/products/biscoff_fantasy.jpeg'],
            ['name' => 'Red Sonata',            'price' => 15.00, 'category' => 'Pastry', 'image' => '/images/products/red_sonata.jpeg'],
            ['name' => 'Baby Pancake',          'price' => 10.00, 'category' => 'Pastry', 'image' => '/images/products/baby_pancake.jpeg'],
            ['name' => 'Banana Supreme',        'price' => 12.00, 'category' => 'Pastry', 'image' => '/images/products/banana_supreme.jpeg'],
            ['name' => 'Pancake Skewer',        'price' => 10.00, 'category' => 'Pastry', 'image' => '/images/products/pancake_skewer.jpeg'],
            ['name' => 'Ice Cream (2 Scoops)',  'price' => 10.00, 'category' => 'Dessert','image' => '/images/products/icecream.png'],
        ];

        foreach ($products as $i => $data) {
            Product::create([
                'name'            => $data['name'],
                'price'           => $data['price'],
                'category'        => $data['category'],
                'image'           => $data['image'],
                'is_available'    => true,
                'is_customizable' => false,
                'sort_order'      => $i,
            ]);
        }

        // ── Your Desire — fully customizable product ──────────────────────────
        $yourDesire = Product::create([
            'name'            => 'Your Desire',
            'description'     => 'Build your own dessert. Choose your ice cream, sauce, and toppings.',
            'price'           => 15.00,
            'category'        => 'Custom',
            'image'           => '/images/products/your_desire.jpeg',
            'is_available'    => true,
            'is_customizable' => true,
            'sort_order'      => count($products),
        ]);

        // Variant group: Ice Cream (checkbox, +RM 2.00)
        $iceCream = $yourDesire->variantGroups()->create([
            'name'           => 'Ice Cream',
            'type'           => 'checkbox',
            'price_modifier' => 2.00,
            'required'       => false,
            'sort_order'     => 0,
        ]);
        $iceCream->options()->create(['name' => 'Add 1 Scoop of Ice Cream', 'extra_price' => 0, 'sort_order' => 0]);

        // Variant group: Sauce (radio, +RM 1.00)
        $sauce = $yourDesire->variantGroups()->create([
            'name'           => 'Sauce',
            'type'           => 'radio',
            'price_modifier' => 1.00,
            'required'       => false,
            'sort_order'     => 1,
        ]);
        foreach (['Caramel', 'Chocolate', 'Strawberry'] as $i => $name) {
            $sauce->options()->create(['name' => $name, 'extra_price' => 0, 'sort_order' => $i]);
        }

        // Variant group: Toppings (multiselect, +RM 0.50 each)
        $toppings = $yourDesire->variantGroups()->create([
            'name'           => 'Toppings',
            'type'           => 'multiselect',
            'price_modifier' => 0.50,
            'required'       => false,
            'sort_order'     => 2,
        ]);
        foreach (['Sprinkles', 'Nuts', 'Cookies'] as $i => $name) {
            $toppings->options()->create(['name' => $name, 'extra_price' => 0, 'sort_order' => $i]);
        }
    }
}
