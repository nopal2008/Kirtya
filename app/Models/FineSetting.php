<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FineSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_rate',
        'max_borrow_days',
        'max_borrow_limit',
        'damage_fee',
        'lost_fee_multiplier',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'daily_rate'          => 'decimal:2',
            'damage_fee'          => 'decimal:2',
            'lost_fee_multiplier' => 'decimal:2',
            'is_active'           => 'boolean',
            'max_borrow_days'     => 'integer',
            'max_borrow_limit'    => 'integer',
        ];
    }

    /**
     * Admin yang membuat konfigurasi denda ini.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Mengambil konfigurasi denda yang sedang aktif.
     */
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->latest()->first();
    }
}
