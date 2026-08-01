<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'isbn',
        'title',
        'author',
        'publisher',
        'publication_year',
        'edition',
        'category',
        'subject',
        'language',
        'dewey_decimal',
        'description',
        'pages',
        'cover_image',
        'rack_location',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'publication_year' => 'integer',
            'pages'            => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Relasi Eloquent
    // -------------------------------------------------------------------------

    /**
     * Satu buku memiliki banyak eksemplar fisik (book stocks).
     */
    public function stocks()
    {
        return $this->hasMany(BookStock::class, 'book_id');
    }

    /**
     * Eksemplar yang tersedia untuk dipinjam.
     */
    public function availableStocks()
    {
        return $this->hasMany(BookStock::class, 'book_id')
                    ->where('status', 'available')
                    ->where('condition', '!=', 'lost');
    }

    // -------------------------------------------------------------------------
    // Accessor
    // -------------------------------------------------------------------------

    /**
     * Jumlah total eksemplar buku ini.
     */
    public function getTotalStockAttribute(): int
    {
        return $this->stocks()->count();
    }

    /**
     * Jumlah eksemplar yang tersedia untuk dipinjam.
     */
    public function getAvailableStockCountAttribute(): int
    {
        return $this->availableStocks()->count();
    }

    // -------------------------------------------------------------------------
    // Scope
    // -------------------------------------------------------------------------

    /**
     * Filter buku berdasarkan ketersediaan stok.
     */
    public function scopeAvailable($query)
    {
        return $query->whereHas('stocks', fn ($q) => $q->where('status', 'available'));
    }

    /**
     * Pencarian katalog OPAC: judul, pengarang, atau ISBN.
     */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('author', 'like', "%{$keyword}%")
              ->orWhere('isbn', 'like', "%{$keyword}%")
              ->orWhere('subject', 'like', "%{$keyword}%");
        });
    }
}
