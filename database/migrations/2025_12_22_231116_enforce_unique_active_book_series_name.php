<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `book_series`
            ADD COLUMN `active_name` VARCHAR(255)
            GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `name`, NULL)) STORED
        ");

        DB::statement("
            CREATE UNIQUE INDEX `book_series_active_name_unique`
            ON `book_series` (`active_name`)
        ");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX `book_series_active_name_unique` ON `book_series`");
        DB::statement("ALTER TABLE `book_series` DROP COLUMN `active_name`");
    }
};
