<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `book_covers`
            ADD COLUMN `active_name` VARCHAR(255)
            GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `name`, NULL)) STORED
        ");

        DB::statement("
            CREATE UNIQUE INDEX `book_covers_active_name_unique`
            ON `book_covers` (`active_name`)
        ");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX `book_covers_active_name_unique` ON `book_covers`");
        DB::statement("ALTER TABLE `book_covers` DROP COLUMN `active_name`");
    }
};
