<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `locations`
            ADD COLUMN `active_name` VARCHAR(255)
            GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `name`, NULL)) STORED
        ");

        DB::statement("
            CREATE UNIQUE INDEX `locations_active_name_unique`
            ON `locations` (`active_name`)
        ");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX `locations_active_name_unique` ON `locations`");
        DB::statement("ALTER TABLE `locations` DROP COLUMN `active_name`");
    }
};
