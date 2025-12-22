<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) book_id NOT NULL (raw SQL om doctrine/dbal dependency te vermijden)
        DB::statement('ALTER TABLE `book_authors` MODIFY `book_id` BIGINT UNSIGNED NOT NULL');

        // 2) Unique constraint op (book_id, author_id)
        Schema::table('book_authors', function (Blueprint $table) {
            $table->unique(['book_id', 'author_id'], 'book_authors_book_id_author_id_unique');
        });
    }

    public function down(): void
    {
        // Drop unique constraint
        Schema::table('book_authors', function (Blueprint $table) {
            $table->dropUnique('book_authors_book_id_author_id_unique');
        });

        // book_id terug nullable
        DB::statement('ALTER TABLE `book_authors` MODIFY `book_id` BIGINT UNSIGNED NULL');
    }
};
