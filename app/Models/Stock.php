<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    protected $fillable = [
        'name',
        'quantity',
        'unit',
        'min_quantity',
        'image_path',
    ];

    protected $casts = [
        'quantity' => 'float',
        'min_quantity' => 'float',
    ];

    public function menuIngredients(): HasMany
    {
        return $this->hasMany(MenuIngredient::class);
    }

    // Status turunan otomatis dari jumlah stok
    public function getStatusAttribute(): string
    {
        if ($this->quantity <= 0) {
            return 'habis';
        }
        if ($this->quantity <= $this->min_quantity) {
            return 'menipis';
        }
        return 'aman';
    }
}