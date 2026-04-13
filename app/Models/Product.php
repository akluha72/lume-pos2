<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'price',
        'image',
        'is_available',
        'is_customizable',
        'sort_order',
    ];

    protected $casts = [
        'price'           => 'decimal:2',
        'is_available'    => 'boolean',
        'is_customizable' => 'boolean',
        'sort_order'      => 'integer',
    ];

    public function variantGroups(): HasMany
    {
        return $this->hasMany(ProductVariantGroup::class)->orderBy('sort_order')->orderBy('id');
    }
}
