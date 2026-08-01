<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel books untuk menyimpan data katalog buku perpustakaan.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            // Identitas bibliografis buku
            $table->string('isbn', 20)->unique()->nullable()->comment('International Standard Book Number');
            $table->string('title')->comment('Judul buku');
            $table->string('author')->comment('Nama pengarang buku');
            $table->string('publisher')->nullable()->comment('Nama penerbit buku');
            $table->smallInteger('publication_year')->nullable()->comment('Tahun terbit buku');
            $table->string('edition', 50)->nullable()->comment('Edisi buku, contoh: Edisi ke-3');

            // Klasifikasi
            $table->string('category')->nullable()->comment('Kategori atau genre buku');
            $table->string('subject')->nullable()->comment('Subyek/topik utama buku');
            $table->string('language', 10)->default('id')->comment('Kode bahasa buku (ISO 639-1)');
            $table->string('dewey_decimal', 30)->nullable()->comment('Nomor klasifikasi Dewey Decimal');

            // Detail fisik dan deskripsi
            $table->text('description')->nullable()->comment('Sinopsis atau deskripsi singkat buku');
            $table->integer('pages')->nullable()->comment('Jumlah halaman buku');
            $table->string('cover_image')->nullable()->comment('Path gambar sampul buku');

            // Lokasi penyimpanan di rak
            $table->string('rack_location', 50)->nullable()->comment('Lokasi rak penyimpanan buku');

            // Status ketersediaan
            $table->enum('status', ['available', 'unavailable'])->default('available')->comment('Status ketersediaan buku');

            $table->timestamps();
            $table->softDeletes()->comment('Soft delete agar data historis tetap terjaga');

            // Index untuk mempercepat pencarian katalog
            $table->index(['title', 'author']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
