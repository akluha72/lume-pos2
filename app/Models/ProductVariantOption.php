<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariantOption extends Model
{
    protected $fillable = [
        'variant_group_id',
        'name',
        'extra_price',
        'sort_order',
    ];

    protected $casts = [
        'extra_price' => 'decimal:2',
        'sort_order'  => 'integer',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ProductVariantGroup::class, 'variant_group_id');
    }
}
