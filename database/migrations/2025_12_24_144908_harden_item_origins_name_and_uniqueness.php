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
        // name NOT NULL (jouw checks tonen dat dit veilig is)
        DB::statement("ALTER TABLE `item_origins` MODIFY `name` VARCHAR(255) NOT NULL");

        if (! $this->columnExists('item_origins', 'active_name')) {
            DB::statement("
                ALTER TABLE `item_origins`
                ADD COLUMN `active_name` VARCHAR(255)
                GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `name`, NULL)) STORED
            ");
        }

        if (! $this->indexExists('item_origins', 'item_origins_active_name_unique')) {
            DB::statement("
                CREATE UNIQUE INDEX `item_origins_active_name_unique`
                ON `item_origins` (`active_name`)
            ");
        }
    }

    public function down(): void
    {
        if ($this->indexExists('item_origins', 'item_origins_active_name_unique')) {
            DB::statement("DROP INDEX `item_origins_active_name_unique` ON `item_origins`");
        }

        if ($this->columnExists('item_origins', 'active_name')) {
            DB::statement("ALTER TABLE `item_origins` DROP COLUMN `active_name`");
        }

        DB::statement("ALTER TABLE `item_origins` MODIFY `name` VARCHAR(255) NULL");
    }
};
