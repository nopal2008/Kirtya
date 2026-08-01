<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'user_id',
        'book_stock_id',
        'processed_by',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'type',
        'booking_expiry',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'borrow_date'    => 'date',
            'due_date'       => 'date',
            'return_date'    => 'date',
            'booking_expiry' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookStock()
    {
        return $this->belongsTo(BookStock::class, 'book_stock_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function fines()
    {
        return $this->hasMany(Fine::class, 'transaction_id');
    }

    public function unpaidFine()
    {
        return $this->hasOne(Fine::class, 'transaction_id')
                    ->where('payment_status', 'unpaid');
    }

    public function getOverdueDaysAttribute(): int
    {
        if ($this->status === 'returned' && $this->return_date) {
            $compareDate = $this->return_date;
        } else {
            $compareDate = now()->toDateString();
        }

        $days = $this->due_date ? $this->due_date->diffInDays($compareDate, false) : 0;

        return $days > 0 ? (int) $days : 0;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'borrowed' => 'bg-blue-100 text-blue-800 ring-blue-600/20',
            'returned' => 'bg-green-100 text-green-800 ring-green-600/20',
            'overdue'  => 'bg-red-100 text-red-800 ring-red-600/20',
            'lost'     => 'bg-gray-100 text-gray-800 ring-gray-600/20',
            default    => 'bg-slate-100 text-slate-700 ring-slate-600/20',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'borrowed'          => 'Dipinjam',
            'returned'          => 'Dikembalikan',
            'overdue'           => 'Terlambat',
            'lost'              => 'Hilang',
            'pending_approval'  => 'Menunggu Persetujuan',
            'rejected'          => 'Ditolak',
            default             => 'Tidak Diketahui',
        };
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['borrowed', 'overdue']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'borrowed')
                     ->where('due_date', '<', now()->toDateString());
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval')->where('type', 'booking');
    }
}
