<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'user_id',
        'type',
        'overdue_days',
        'daily_rate',
        'amount',
        'payment_status',
        'paid_at',
        'paid_by',
        'waived_reason',
    ];

    protected function casts(): array
    {
        return [
            'daily_rate'   => 'decimal:2',
            'amount'       => 'decimal:2',
            'paid_at'      => 'datetime',
            'overdue_days' => 'integer',
        ];
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paidByUser()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'overdue' => 'Keterlambatan',
            'damage'  => 'Kerusakan',
            'lost'    => 'Kehilangan',
            default   => 'Lainnya',
        };
    }

    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return match ($this->payment_status) {
            'unpaid' => 'bg-red-100 text-red-800 ring-red-600/20',
            'paid'   => 'bg-green-100 text-green-800 ring-green-600/20',
            'waived' => 'bg-amber-100 text-amber-800 ring-amber-600/20',
            default  => 'bg-slate-100 text-slate-700 ring-slate-600/20',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'unpaid' => 'Belum Dibayar',
            'paid'   => 'Lunas',
            'waived' => 'Dibebaskan',
            default  => 'Tidak Diketahui',
        };
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}
