<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'barcode',
        'accession_number',
        'condition',
        'status',
        'acquisition_date',
        'acquisition_source',
        'acquisition_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'acquisition_date'  => 'date',
            'acquisition_price' => 'decimal:2',
        ];
    }

    // -------------------------------------------------------------------------
    // Relasi Eloquent
    // -------------------------------------------------------------------------

    /**
     * Satu eksemplar milik satu buku induk.
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    /**
     * Satu eksemplar dapat memiliki banyak riwayat transaksi peminjaman.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'book_stock_id');
    }

    /**
     * Transaksi aktif (buku sedang dipinjam).
     */
    public function activeTransaction()
    {
        return $this->hasOne(Transaction::class, 'book_stock_id')
                    ->whereIn('status', ['borrowed', 'overdue']);
    }

    // -------------------------------------------------------------------------
    // Scope
    // -------------------------------------------------------------------------

    /**
     * Filter hanya eksemplar yang tersedia untuk dipinjam.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('condition', '!=', 'lost');
    }

    /**
     * Filter hanya eksemplar yang sedang dipinjam.
     */
    public function scopeBorrowed($query)
    {
        return $query->where('status', 'borrowed');
    }
}
