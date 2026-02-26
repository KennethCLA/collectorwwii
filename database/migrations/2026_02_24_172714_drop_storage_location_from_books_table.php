<?php
// database/migrations/2026_02_24_172714_drop_storage_location_from_books_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'storage_location')) {
                $table->dropColumn('storage_location');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'storage_location')) {
                $table->string('storage_location')->nullable();
            }
        });
    }
};
