<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Voeg de generated column toe (STORED is indexeerbaar)
        DB::statement("
            ALTER TABLE `countries`
            ADD COLUMN `active_name` VARCHAR(255)
            GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `name`, NULL)) STORED
        ");

        // Unique index op active_name => unieke actieve namen, soft-deleted mag dupliceren
        DB::statement("
            CREATE UNIQUE INDEX `countries_active_name_unique`
            ON `countries` (`active_name`)
        ");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX `countries_active_name_unique` ON `countries`");
        DB::statement("ALTER TABLE `countries` DROP COLUMN `active_name`");
    }
};
