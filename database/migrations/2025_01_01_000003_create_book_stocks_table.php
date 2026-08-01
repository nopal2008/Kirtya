<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel book_stocks untuk menyimpan data eksemplar fisik tiap buku.
     * Satu buku (books) dapat memiliki banyak eksemplar (book_stocks).
     */
    public function up(): void
    {
        Schema::create('book_stocks', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel induk buku
            $table->foreignId('book_id')
                  ->constrained('books')
                  ->onDelete('cascade')
                  ->comment('Referensi ke tabel books');

            // Identifikasi unik eksemplar
            $table->string('barcode', 50)->unique()->comment('Kode barcode unik tiap eksemplar buku');
            $table->string('accession_number', 30)->unique()->nullable()->comment('Nomor induk buku (NIB)');

            // Kondisi dan status fisik eksemplar
            $table->enum('condition', ['good', 'damaged', 'lost'])->default('good')->comment('Kondisi fisik eksemplar');
            $table->enum('status', ['available', 'borrowed', 'reserved', 'maintenance'])->default('available')->comment('Status ketersediaan eksemplar');

            // Informasi pengadaan
            $table->date('acquisition_date')->nullable()->comment('Tanggal pengadaan eksemplar');
            $table->string('acquisition_source')->nullable()->comment('Sumber pengadaan: pembelian, hibah, dll');
            $table->decimal('acquisition_price', 12, 2)->nullable()->comment('Harga pengadaan eksemplar dalam Rupiah');

            // Catatan tambahan kondisi fisik
            $table->text('notes')->nullable()->comment('Catatan kondisi atau keterangan tambahan eksemplar');

            $table->timestamps();

            // Index untuk mempercepat pencarian berdasarkan status
            $table->index('status');
            $table->index('condition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_stocks');
    }
};
