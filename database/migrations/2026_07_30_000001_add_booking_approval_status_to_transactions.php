<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah status pending_approval & rejected ke kolom status transactions.
     */
    public function up(): void
    {
        // MySQL: modify enum column
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('borrowed','returned','overdue','lost','pending_approval','rejected') NOT NULL DEFAULT 'borrowed'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('borrowed','returned','overdue','lost') NOT NULL DEFAULT 'borrowed'");
    }
};
