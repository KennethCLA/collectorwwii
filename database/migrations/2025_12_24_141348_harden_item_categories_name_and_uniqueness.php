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
        // name NOT NULL (is veilig volgens jouw checks)
        DB::statement("ALTER TABLE `item_categories` MODIFY `name` VARCHAR(255) NOT NULL");

        // active_name (soft-delete proof)
        if (! $this->columnExists('item_categories', 'active_name')) {
            DB::statement("
                ALTER TABLE `item_categories`
                ADD COLUMN `active_name` VARCHAR(255)
                GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `name`, NULL)) STORED
            ");
        }

        // unique index op active_name
        if (! $this->indexExists('item_categories', 'item_categories_active_name_unique')) {
            DB::statement("
                CREATE UNIQUE INDEX `item_categories_active_name_unique`
                ON `item_categories` (`active_name`)
            ");
        }
    }

    public function down(): void
    {
        if ($this->indexExists('item_categories', 'item_categories_active_name_unique')) {
            DB::statement("DROP INDEX `item_categories_active_name_unique` ON `item_categories`");
        }

        if ($this->columnExists('item_categories', 'active_name')) {
            DB::statement("ALTER TABLE `item_categories` DROP COLUMN `active_name`");
        }

        // terug naar nullable zoals origineel
        DB::statement("ALTER TABLE `item_categories` MODIFY `name` VARCHAR(255) NULL");
    }
};
