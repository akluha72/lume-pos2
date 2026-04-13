<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariantGroup extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'type',
        'price_modifier',
        'required',
        'sort_order',
    ];

    protected $casts = [
        'price_modifier' => 'decimal:2',
        'required'       => 'boolean',
        'sort_order'     => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductVariantOption::class, 'variant_group_id')
                    ->orderBy('sort_order')
                    ->orderBy('id');
    }
}
