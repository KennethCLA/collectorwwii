<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    private function columnExists(string $table, string $column): bool
    {
        $db = DB::getDatabaseName();
        $res = DB::selectOne(
            "SELECT COUNT(*) AS c
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?",
            [$db, $table, $column]
        );
        return (int)($res->c ?? 0) > 0;
    }

    private function indexExists(string $table, string $index): bool
    {
        $db = DB::getDatabaseName();
        $res = DB::selectOne(
            "SELECT COUNT(*) AS c
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?",
            [$db, $table, $index]
        );
        return (int)($res->c ?? 0) > 0;
    }

    public function up(): void
    {
        if (! $this->columnExists('colours', 'active_name')) {
            DB::statement("
                ALTER TABLE `colours`
                ADD COLUMN `active_name` VARCHAR(255)
                GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `name`, NULL)) STORED
            ");
        }

        if (! $this->indexExists('colours', 'colours_active_name_unique')) {
            DB::statement("
                CREATE UNIQUE INDEX `colours_active_name_unique`
                ON `colours` (`active_name`)
            ");
        }
    }

    public function down(): void
    {
        if ($this->indexExists('colours', 'colours_active_name_unique')) {
            DB::statement("DROP INDEX `colours_active_name_unique` ON `colours`");
        }

        if ($this->columnExists('colours', 'active_name')) {
            DB::statement("ALTER TABLE `colours` DROP COLUMN `active_name`");
        }
    }
};
