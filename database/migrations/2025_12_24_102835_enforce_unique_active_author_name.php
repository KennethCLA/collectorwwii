<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `authors`
            ADD COLUMN `active_name` VARCHAR(255)
            GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `name`, NULL)) STORED
        ");

        DB::statement("
            CREATE UNIQUE INDEX `authors_active_name_unique`
            ON `authors` (`active_name`)
        ");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX `authors_active_name_unique` ON `authors`");
        DB::statement("ALTER TABLE `authors` DROP COLUMN `active_name`");
    }
};
