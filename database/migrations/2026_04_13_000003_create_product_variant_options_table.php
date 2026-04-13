<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variant_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_group_id')
                  ->constrained('product_variant_groups')
                  ->cascadeOnDelete();
            $table->string('name');                              // e.g. "Caramel", "Sprinkles"
            $table->decimal('extra_price', 10, 2)->default(0.00); // on top of group price_modifier
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_options');
    }
};
