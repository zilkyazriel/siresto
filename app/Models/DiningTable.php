<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiningTable extends Model
{
    protected $fillable = ['number', 'capacity', 'status', 'cleaned_at'];

    protected $casts = [
        'cleaned_at' => 'datetime',
    ];

    public const STATUSES = ['tersedia', 'terisi', 'kotor', 'reserved'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'tersedia' => 'Tersedia',
            'terisi'   => 'Terisi',
            'kotor'    => 'Perlu Dibersihkan',
            'reserved' => 'Reserved',
            default    => ucfirst((string) ($this->status ?? 'tersedia')),
        };
    }

    public function markAvailable(): void
    {
        $this->update([
            'status'     => 'tersedia',
            'cleaned_at' => now(),   // catat waktu pembersihan
        ]);
    }

    /**
     * Pro-13: hitung ulang status meja dari pesanan HARI INI.
     * - masih ada pesanan belum dibayar                 -> terisi
     * - ada pesanan lunas SETELAH pembersihan terakhir   -> kotor
     * - selain itu                                        -> tersedia
     */
    public function refreshStatusFromOrders(): void
    {
        $base = $this->orders()
            ->where('status', '!=', 'batal')
            ->whereDate('created_at', today());

        // 1. Masih ada pesanan belum dibayar -> meja terisi.
        if ((clone $base)->doesntHave('payment')->exists()) {
            $this->update(['status' => 'terisi']);
            return;
        }

        // 2. Ada pesanan lunas yang dibayar SETELAH pembersihan terakhir -> perlu dibersihkan.
        $cleanedAt = $this->cleaned_at;
        $adaLunasBaru = (clone $base)
            ->whereHas('payment', function ($q) use ($cleanedAt) {
                $q->where('paid', true);
                if ($cleanedAt) {
                    $q->where('paid_at', '>', $cleanedAt);
                }
            })
            ->exists();

        if ($adaLunasBaru) {
            $this->update(['status' => 'kotor']);
            return;
        }

        // 3. Tidak ada aktivitas baru -> meja tersedia.
        $this->update(['status' => 'tersedia']);
    }
}