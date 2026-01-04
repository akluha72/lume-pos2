@extends('layouts.app')

@section('title', 'Point of Sale')

@php
$products = $products ?? [
    ['id' => 1, 'name' => 'Classic Butterscotch', 'price' => 9.90, 'category' => 'Pastry', 'isAvailable' => true, 'isCustomizable' => false, 'image' => '/images/products/classic_butterscotch.jpeg'],
    ['id' => 2, 'name' => 'Choco Addiction', 'price' => 12.00, 'category' => 'Pastry', 'isAvailable' => true, 'isCustomizable' => false, 'image' => '/images/products/choco_addiction.jpeg'],
    ['id' => 3, 'name' => 'Berries Symphony', 'price' => 18.00, 'category' => 'Pastry', 'isAvailable' => true, 'isCustomizable' => false, 'image' => '/images/products/berries_symphony.jpeg'],
    ['id' => 4, 'name' => 'Biscoff Fantasy', 'price' => 15.00, 'category' => 'Pastry', 'isAvailable' => true, 'isCustomizable' => false, 'image' => '/images/products/biscoff_fantasy.jpeg'],
    ['id' => 5, 'name' => 'Red Sonata', 'price' => 15.00, 'category' => 'Pastry', 'isAvailable' => true, 'isCustomizable' => false, 'image' => '/images/products/red_sonata.jpeg'],
    ['id' => 6, 'name' => 'Baby Pancake', 'price' => 10.00, 'category' => 'Pastry', 'isAvailable' => true, 'isCustomizable' => false, 'image' => '/images/products/baby_pancake.jpeg'],
    ['id' => 7, 'name' => 'Banana Supreme', 'price' => 12.00, 'category' => 'Pastry', 'isAvailable' => true, 'isCustomizable' => false, 'image' => '/images/products/banana_supreme.jpeg'],
    ['id' => 8, 'name' => 'Pancake Skewer', 'price' => 10.00, 'category' => 'Pastry', 'isAvailable' => true, 'isCustomizable' => false, 'image' => '/images/products/pancake_skewer.jpeg'],
    ['id' => 9, 'name' => 'Ice Cream (2 Scope)', 'price' => 10.00, 'category' => 'Pastry', 'isAvailable' => true, 'isCustomizable' => false, 'image' => '/images/products/icecream.png'],
    ['id' => 10, 'name' => 'Your Desire', 'price' => 15.00, 'category' => 'Custom', 'isAvailable' => true, 'isCustomizable' => true, 'image' => '/images/products/your_desire.jpeg']
];
@endphp

@section('content')
<div class="flex h-full overflow-hidden" x-data="posSystem()">
    <!-- Left: Product Selection -->
    <div class="w-2/3 bg-white p-6 overflow-y-auto flex flex-col h-full">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Menu</h2>
            <div class="relative w-64">
                <input 
                    type="text" 
                    placeholder="Search product..." 
                    class="w-full pl-4 pr-10 py-2 border border-gray-200 rounded-full text-sm outline-none focus:ring-2 focus:ring-yellow-300"
                    x-model="search"
                />
            </div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <template x-for="product in filteredProducts" :key="product.id">
                <button 
                    type="button"
                    @click="product.isCustomizable ? openCustomizer(product) : addToCart(product)"
                    class="p-4 border border-gray-200 rounded-xl hover:bg-amber-50 hover:border-gray-300 transition-all text-left flex flex-col items-center gap-3 group relative"
                >
                    <span x-show="product.isCustomizable" class="absolute top-2 right-2 bg-yellow-100 text-yellow-800 text-[10px] px-2 py-0.5 rounded-full font-bold">CUSTOM</span>
                    <img :src="product.image" :alt="product.name" class="w-full h-32 object-cover rounded-lg group-hover:scale-105 transition-transform">
                    <div class="w-full">
                        <p class="font-semibold text-gray-800 truncate" x-text="product.name"></p>
                        <p class="text-yellow-600 font-bold" x-text="'RM ' + product.price.toFixed(2)"></p>
                    </div>
                </button>
            </template>
        </div>
    </div>

    <!-- Right: Cart & Queue Summary -->
    <div class="w-1/3 bg-gray-50 p-6 flex flex-col ">
        <div class="bg-white p-6 rounded-2xl shadow-sm flex flex-col flex-1 h-full">
            <h3 class="text-lg font-bold mb-4 border-b border-gray-200 pb-4">Current Transaction</h3>
            
            <div class="flex-1 overflow-y-auto space-y-4 mb-4">
                <div x-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-gray-400 italic">
                    <span class="text-4xl mb-2">🛒</span>
                    No items selected
                </div>
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="border-b border-gray-200 pb-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium" x-text="item.name"></p>
                                <p class="text-xs text-gray-500" x-text="'RM ' + item.price.toFixed(2) + ' x ' + item.quantity"></p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="font-bold" x-text="'RM ' + (item.price * item.quantity).toFixed(2)"></span>
                                <button @click="removeFromCart(index)" class="text-red-400 hover:text-red-600">✕</button>
                            </div>
                        </div>
                        <template x-if="item.customizations && item.customizations.length">
                            <div class="flex flex-wrap gap-1 mt-1">
                                <template x-for="custom in item.customizations" :key="custom">
                                    <span class="text-[9px] bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded" x-text="custom"></span>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="border-t border-gray-200 pt-4 space-y-3">
                <div class="flex justify-between text-gray-600 font-semibold">
                    <span>Subtotal</span>
                    <span x-text="'RM ' + subtotal.toFixed(2)"></span>
                </div>
                <div class="flex justify-between text-black font-extrabold text-lg pt-2 border-t border-gray-200 border-dashed">
                    <span>Total</span>
                    <span x-text="'RM ' + total.toFixed(2)"></span>
                </div>
                
                <div class="grid grid-cols-2 gap-3 mt-4">
                    <button type="button" @click="paymentMethod = 'qr'" :class="paymentMethod === 'qr' ? 'border-yellow-300 text-yellow-300 bg-yellow-300/20' : 'border-yellow-300 text-yellow-300'" class="py-3 border font-bold rounded-xl hover:bg-yellow-300/20">DuitNow QR</button>
                    <button type="button" @click="paymentMethod = 'cash'" :class="paymentMethod === 'cash' ? 'border-yellow-300 text-yellow-300 bg-yellow-300/20' : 'border-yellow-300 text-yellow-300'" class="py-3 border font-bold rounded-xl hover:bg-yellow-300/20 text-black">Cash</button>
                </div>
                <button 
                    type="button"
                    @click="completeSale()"
                    :disabled="cart.length === 0"
                    class="w-full py-4 bg-yellow-300 text-black font-bold rounded-xl shadow-lg hover:bg-yellow-400 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Submit Order
                </button>
            </div>
        </div>
    </div>

    <!-- Customizer Modal -->
    <div x-show="showCustomizer" x-transition class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4" @click.self="showCustomizer = false" style="display: none;">
        <div class="bg-white w-full max-w-lg rounded-3xl p-8 shadow-2xl max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold" x-text="customizingProduct?.name ? 'Customize ' + customizingProduct.name : 'Customize'"></h2>
                <button @click="showCustomizer = false" class="text-gray-400 text-2xl">✕</button>
            </div>

            <div class="space-y-6">
                <!-- Ice Cream Section -->
                <div>
                    <h3 class="font-bold text-gray-700 mb-2">Ice Cream <span class="font-normal text-sm">(RM 2.00)</span></h3>
                    <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:bg-gray-50"
                        :class="customization.iceCream ? 'border-yellow-300 bg-yellow-300/20 font-bold text-yellow-700' : 'border-gray-200'">
                        <input type="checkbox" x-model="customization.iceCream" value="vanilla" class="w-5 h-5 rounded border-gray-300 text-yellow-300" />
                        <span class="font-medium">Add 1 Scoop of Ice Cream</span>
                    </label>
                </div>

                <!-- Sauce Section -->
                <div>
                    <h3 class="font-bold text-gray-700 mb-2">Sauce <span class="font-normal text-sm">(RM 1.00)</span></h3>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="s in ['caramel','chocolate','strawberry','none']" :key="s">
                            <button type="button" @click="customization.sauce = s" 
                                class="p-3 text-sm rounded-xl border-2 transition-all"
                                :class="customization.sauce === s ? 'border-yellow-300 bg-yellow-300/20 font-bold text-yellow-700' : 'border-gray-200 hover:border-gray-300'">
                                <span x-text="s.charAt(0).toUpperCase() + s.slice(1)"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Toppings Section -->
                <div>
                    <h3 class="font-bold text-gray-700 mb-2">Toppings <span class="font-normal text-sm">(RM 0.50 each)</span></h3>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="t in ['sprinkles','nuts','cookies','none']" :key="t">
                            <button type="button" @click="toggleTopping(t)"
                                class="p-3 text-sm text-left rounded-xl border-2 transition-all"
                                :class="customization.toppings.includes(t) ? 'border-yellow-300 bg-yellow-300/20 font-bold text-yellow-700' : 'border-gray-200 hover:border-gray-300'">
                                <span x-text="t.charAt(0).toUpperCase() + t.slice(1)"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 mt-8 pt-6 border-t">
                <button @click="showCustomizer = false" class="flex-1 py-3 border border-gray-300 rounded-xl font-bold">Cancel</button>
                <button @click="addCustomizedProduct()" class="flex-1 py-3 bg-yellow-300 text-black rounded-xl font-bold hover:bg-yellow-400">
                    Add to Order (RM <span x-text="customizerTotal().toFixed(2)"></span>)
                </button>
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
        showCustomizer: false,
        customizingProduct: null,
        customization: {
            iceCream: false, // boolean for checkbox
            sauce: 'none',
            toppings: []
        },

        get filteredProducts() {
            return this.products.filter(p => 
                p.name.toLowerCase().includes(this.search.toLowerCase())
            );
        },

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        get tax() {
            return this.subtotal * 0.06;
        },

        get total() {
            return this.subtotal + this.tax;
        },

        addToCart(product) {
            const existingItem = this.cart.find(item => item.id === product.id);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                this.cart.push({ ...product, quantity: 1 });
            }
        },

        openCustomizer(product) {
            this.customizingProduct = product;
            this.showCustomizer = true;
            this.customization = {
                iceCream: false,
                sauce: 'none',
                toppings: []
            };
        },

        toggleTopping(t) {
            if (t === 'none') {
                this.customization.toppings = ['none'];
            } else {
                if (this.customization.toppings.includes('none')) {
                    this.customization.toppings = [];
                }
                if (this.customization.toppings.includes(t)) {
                    this.customization.toppings = this.customization.toppings.filter(x => x !== t);
                } else {
                    this.customization.toppings.push(t);
                }
            }
        },

        customizerTotal() {
            let total = this.customizingProduct ? this.customizingProduct.price : 0;
            if (this.customization.iceCream) total += 2.00;
            if (this.customization.sauce && this.customization.sauce !== 'none') total += 1.00;
            if (this.customization.toppings && this.customization.toppings.length > 0) {
                total += this.customization.toppings.filter(t => t !== 'none').length * 0.5;
            }
            return total;
        },

        addCustomizedProduct() {
            if (!this.customizingProduct) return;
            let customizations = [];
            if (this.customization.iceCream) customizations.push('+ Ice Cream');
            if (this.customization.sauce && this.customization.sauce !== 'none') customizations.push('+ ' + this.customization.sauce.charAt(0).toUpperCase() + this.customization.sauce.slice(1) + ' Sauce');
            if (this.customization.toppings && this.customization.toppings.length > 0 && !this.customization.toppings.includes('none')) {
                this.customization.toppings.forEach(t => customizations.push('+ ' + t.charAt(0).toUpperCase() + t.slice(1)));
            }
            const customizedItem = {
                ...this.customizingProduct,
                quantity: 1,
                price: this.customizerTotal(),
                customizations
            };
            this.cart.push(customizedItem);
            this.showCustomizer = false;
            this.customizingProduct = null;
        },

        updateQuantity(index, newQuantity) {
            if (newQuantity <= 0) {
                this.cart.splice(index, 1);
            } else {
                this.cart[index].quantity = newQuantity;
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        completeSale() {
            if (this.cart.length === 0) return;
            alert(`Sale completed!\nTotal: RM${this.total.toFixed(2)}\nPayment: ${this.paymentMethod}`);
            this.cart = [];
            this.paymentMethod = 'cash';
        }
    }
}
</script>
@endsection