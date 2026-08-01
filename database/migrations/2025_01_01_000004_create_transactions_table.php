<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel transactions untuk mencatat sirkulasi peminjaman dan pengembalian buku.
     * Satu transaksi merepresentasikan satu sesi peminjaman satu eksemplar buku oleh satu anggota.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Nomor transaksi unik sebagai referensi eksternal
            $table->string('transaction_code', 30)->unique()->comment('Kode transaksi unik, contoh: TRX-20250101-0001');

            // Relasi ke anggota yang meminjam
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict')
                  ->comment('Referensi ke tabel users (anggota peminjam)');

            // Relasi ke eksemplar buku yang dipinjam
            $table->foreignId('book_stock_id')
                  ->constrained('book_stocks')
                  ->onDelete('restrict')
                  ->comment('Referensi ke tabel book_stocks (eksemplar buku yang dipinjam)');

            // Petugas yang memproses transaksi
            $table->foreignId('processed_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Referensi ke tabel users (petugas admin yang memproses)');

            // Tanggal sirkulasi
            $table->date('borrow_date')->comment('Tanggal peminjaman buku');
            $table->date('due_date')->comment('Tanggal jatuh tempo pengembalian buku');
            $table->date('return_date')->nullable()->comment('Tanggal pengembalian aktual; null jika belum dikembalikan');

            // Status peminjaman
            $table->enum('status', ['borrowed', 'returned', 'overdue', 'lost'])->default('borrowed')->comment('Status sirkulasi peminjaman');

            // Booking (reservasi buku sebelum dipinjam)
            $table->enum('type', ['borrow', 'booking'])->default('borrow')->comment('Jenis transaksi: peminjaman langsung atau booking');
            $table->timestamp('booking_expiry')->nullable()->comment('Batas waktu booking; null untuk peminjaman biasa');

            // Catatan petugas
            $table->text('notes')->nullable()->comment('Catatan tambahan dari petugas');

            $table->timestamps();

            // Index untuk mempercepat query laporan dan filter status
            $table->index('status');
            $table->index('borrow_date');
            $table->index('due_date');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
