<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'subtotal',
        'total',
        'payment_method',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Formatted order number e.g. #1001 */
    public function getOrderNumberAttribute(): string
    {
        return '#' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}
