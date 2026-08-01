<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'member_id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'photo',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // -------------------------------------------------------------------------
    // Relasi Eloquent
    // -------------------------------------------------------------------------

    /**
     * Satu user memiliki banyak transaksi peminjaman sebagai peminjam.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Satu user memiliki banyak denda.
     */
    public function fines()
    {
        return $this->hasMany(Fine::class, 'user_id');
    }

    /**
     * Transaksi yang diproses oleh petugas ini.
     */
    public function processedTransactions()
    {
        return $this->hasMany(Transaction::class, 'processed_by');
    }

    /**
     * Log kunjungan anggota ini.
     */
    public function visitorLogs()
    {
        return $this->hasMany(VisitorLog::class, 'user_id');
    }

    /**
     * Audit log yang dibuat oleh user ini.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    // -------------------------------------------------------------------------
    // Accessor
    // -------------------------------------------------------------------------

    /**
     * Mengembalikan total denda yang belum dibayar oleh user ini.
     */
    public function getUnpaidFinesTotalAttribute(): float
    {
        return $this->fines()->where('payment_status', 'unpaid')->sum('amount');
    }

    /**
     * Memeriksa apakah user memiliki MINIMAL 1 denda yang belum dibayar (berapa pun nominalnya).
     * Digunakan sebagai blokir keras: jika ada 1 denda pun, tidak bisa booking/pinjam.
     */
    public function getHasAnyFineAttribute(): bool
    {
        return $this->fines()->where('payment_status', 'unpaid')->exists();
    }

    /**
     * Memeriksa apakah user sedang memiliki buku yang terlambat dikembalikan.
     */
    public function getHasOverdueBooksAttribute(): bool
    {
        return $this->transactions()
                    ->where('status', 'overdue')
                    ->exists();
    }
}
