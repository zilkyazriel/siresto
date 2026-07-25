<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'code',
        'customer_name',
        'customer_phone',
        'dining_table_id',
        'reserved_at',
        'party_size',
        'status',
        'note',
        'user_id',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diningTable(): BelongsTo
    {
        return $this->belongsTo(DiningTable::class);
    }
}