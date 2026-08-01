<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel fines untuk menyimpan data denda keterlambatan pengembalian buku,
     * serta tabel fine_settings untuk konfigurasi tarif denda oleh Admin.
     */
    public function up(): void
    {
        // Tabel konfigurasi denda yang dapat diatur oleh Admin
        Schema::create('fine_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('daily_rate', 10, 2)->default(1000.00)->comment('Tarif denda per hari dalam Rupiah');
            $table->integer('max_borrow_days')->default(7)->comment('Maksimal hari peminjaman sebelum terkena denda');
            $table->integer('max_borrow_limit')->default(3)->comment('Batas maksimal buku yang dapat dipinjam sekaligus');
            $table->decimal('damage_fee', 12, 2)->default(50000.00)->comment('Biaya denda kerusakan buku dalam Rupiah');
            $table->decimal('lost_fee_multiplier', 5, 2)->default(2.00)->comment('Pengali harga buku untuk denda kehilangan');
            $table->boolean('is_active')->default(true)->comment('Menandai apakah konfigurasi ini sedang aktif');
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Admin yang membuat konfigurasi ini');
            $table->timestamps();
        });

        // Tabel denda yang dikenakan ke anggota per transaksi peminjaman
        Schema::create('fines', function (Blueprint $table) {
            $table->id();

            // Relasi ke transaksi peminjaman yang menghasilkan denda ini
            $table->foreignId('transaction_id')
                  ->constrained('transactions')
                  ->onDelete('restrict')
                  ->comment('Referensi ke tabel transactions');

            // Relasi ke anggota yang dikenai denda
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict')
                  ->comment('Referensi ke tabel users (anggota yang dikenai denda)');

            // Penyebab dan detail perhitungan denda
            $table->enum('type', ['overdue', 'damage', 'lost'])->comment('Jenis denda: keterlambatan, kerusakan, atau kehilangan buku');
            $table->integer('overdue_days')->default(0)->comment('Jumlah hari keterlambatan; 0 untuk denda non-keterlambatan');
            $table->decimal('daily_rate', 10, 2)->nullable()->comment('Tarif harian yang berlaku saat denda dihitung');
            $table->decimal('amount', 12, 2)->comment('Total jumlah denda dalam Rupiah');

            // Status pembayaran denda
            $table->enum('payment_status', ['unpaid', 'paid', 'waived'])->default('unpaid')->comment('Status pembayaran denda');
            $table->timestamp('paid_at')->nullable()->comment('Waktu pembayaran denda dilakukan');

            // Petugas yang memproses pembayaran denda
            $table->foreignId('paid_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Petugas admin yang memproses pembayaran denda');

            // Catatan pembebasan denda (waived)
            $table->text('waived_reason')->nullable()->comment('Alasan pembebasan denda jika payment_status = waived');

            $table->timestamps();

            // Index untuk mempercepat query laporan keuangan
            $table->index('payment_status');
            $table->index('type');
        });

        // Tabel audit log untuk mencatat perubahan penting pada sistem
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Pengguna yang melakukan aksi');
            $table->string('action')->comment('Nama aksi yang dilakukan, contoh: create_book, delete_user');
            $table->string('model_type')->nullable()->comment('Nama model yang terdampak, contoh: App\\Models\\Book');
            $table->unsignedBigInteger('model_id')->nullable()->comment('ID record model yang terdampak');
            $table->json('old_values')->nullable()->comment('Nilai data sebelum perubahan (JSON)');
            $table->json('new_values')->nullable()->comment('Nilai data setelah perubahan (JSON)');
            $table->string('ip_address', 45)->nullable()->comment('Alamat IP pengguna saat aksi dilakukan');
            $table->text('user_agent')->nullable()->comment('User agent browser pengguna');
            $table->timestamps();

            $table->index('action');
            $table->index(['model_type', 'model_id']);
        });

        // Tabel buku tamu untuk mencatat kunjungan ke perpustakaan
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Relasi ke tabel users jika pengunjung adalah anggota terdaftar');
            $table->string('visitor_name')->nullable()->comment('Nama pengunjung (untuk tamu non-anggota)');
            $table->string('institution')->nullable()->comment('Asal instansi atau kelas pengunjung');
            $table->enum('purpose', ['reading', 'borrowing', 'returning', 'studying', 'other'])->default('other')->comment('Tujuan kunjungan ke perpustakaan');
            $table->timestamp('check_in_at')->comment('Waktu masuk perpustakaan');
            $table->timestamp('check_out_at')->nullable()->comment('Waktu keluar perpustakaan');
            $table->foreignId('processed_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Petugas yang mencatat kunjungan');
            $table->timestamps();

            $table->index('check_in_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('fines');
        Schema::dropIfExists('fine_settings');
    }
};
