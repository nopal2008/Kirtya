<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'visitor_name',
        'institution',
        'purpose',
        'check_in_at',
        'check_out_at',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'check_in_at'  => 'datetime',
            'check_out_at' => 'datetime',
        ];
    }

    /**
     * Anggota terdaftar yang berkunjung.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Petugas yang mencatat kunjungan.
     */
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Durasi kunjungan dalam menit.
     */
    public function getDurationMinutesAttribute(): ?int
    {
        if ($this->check_out_at) {
            return (int) $this->check_in_at->diffInMinutes($this->check_out_at);
        }

        return null;
    }
}
