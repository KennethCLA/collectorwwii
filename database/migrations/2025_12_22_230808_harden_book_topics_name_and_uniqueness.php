<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) name NOT NULL
        DB::statement("ALTER TABLE `book_topics` MODIFY `name` VARCHAR(255) NOT NULL");

        // 2) active_name generated column (soft-delete proof)
        DB::statement("
            ALTER TABLE `book_topics`
            ADD COLUMN `active_name` VARCHAR(255)
            GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `name`, NULL)) STORED
        ");

        // 3) unique index op active_name => unieke actieve topics
        DB::statement("
            CREATE UNIQUE INDEX `book_topics_active_name_unique`
            ON `book_topics` (`active_name`)
        ");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX `book_topics_active_name_unique` ON `book_topics`");
        DB::statement("ALTER TABLE `book_topics` DROP COLUMN `active_name`");

        // terug nullable zoals oorspronkelijk
        DB::statement("ALTER TABLE `book_topics` MODIFY `name` VARCHAR(255) NULL");
    }
};
