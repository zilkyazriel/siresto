<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockEntryItem extends Model
{
    protected $fillable = [
        'stock_entry_id',
        'stock_id',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'float',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function stockEntry(): BelongsTo
    {
        return $this->belongsTo(StockEntry::class);
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}