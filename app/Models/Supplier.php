<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_name',
        'phone',
        'category',
        'address',
    ];

    public const CATEGORIES = [
        'basah' => 'Bahan Basah',
        'kering' => 'Bahan Kering',
        'minuman' => 'Minuman',
        'peralatan' => 'Peralatan',
    ];

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getInitialsAttribute(): string
    {
        $skip = ['pt', 'cv', 'ud', 'pt.', 'cv.', 'ud.', 'toko'];
        $words = preg_split('/\s+/', trim((string) $this->name));
        $letters = '';
        foreach ($words as $w) {
            if ($w === '' || in_array(mb_strtolower($w), $skip, true)) {
                continue;
            }
            $letters .= mb_substr($w, 0, 1);
            if (mb_strlen($letters) >= 2) {
                break;
            }
        }
        if ($letters === '') {
            $letters = mb_substr((string) $this->name, 0, 2);
        }
        return mb_strtoupper($letters);
    }
}
