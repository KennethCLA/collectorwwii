<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The old migrations added a global UNIQUE index on an `active_name` generated column
     * (name when not soft-deleted, else NULL). This is incompatible with a tree structure
     * where the same name can legitimately appear under different parents.
     *
     * We replace it with a parent-scoped unique index: UNIQUE(name, parent_id).
     * Siblings (same parent) must still be unique, but the same name may exist under
     * different parents.
     */
    private array $tables = [
        // table              old index name                          new index name
        ['book_topics',   'book_topics_active_name_unique',   'book_topics_name_parent_unique'],
        ['item_categories', 'item_categories_active_name_unique', 'item_categories_name_parent_unique'],
        ['locations',     'locations_active_name_unique',     'locations_name_parent_unique'],
        ['origins',       'item_origins_active_name_unique',  'origins_name_parent_unique'],
    ];

    public function up(): void
    {
        foreach ($this->tables as [$table, $oldIndex, $newIndex]) {
            // Drop old global unique index
            if ($this->indexExists($table, $oldIndex)) {
                DB::statement("DROP INDEX `{$oldIndex}` ON `{$table}`");
            }

            // Drop the generated active_name column
            if (Schema::hasColumn($table, 'active_name')) {
                DB::statement("ALTER TABLE `{$table}` DROP COLUMN `active_name`");
            }

            // Add parent-scoped unique index: unique name within the same parent
            if (! $this->indexExists($table, $newIndex)) {
                DB::statement("CREATE UNIQUE INDEX `{$newIndex}` ON `{$table}` (`name`, `parent_id`)");
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as [$table, $oldIndex, $newIndex]) {
            // Drop the new index
            if ($this->indexExists($table, $newIndex)) {
                DB::statement("DROP INDEX `{$newIndex}` ON `{$table}`");
            }

            // Restore the active_name generated column
            if (! Schema::hasColumn($table, 'active_name')) {
                DB::statement("
                    ALTER TABLE `{$table}`
                    ADD COLUMN `active_name` VARCHAR(255)
                    GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `name`, NULL)) STORED
                ");
            }

            // Restore the global unique index
            if (! $this->indexExists($table, $oldIndex)) {
                DB::statement("CREATE UNIQUE INDEX `{$oldIndex}` ON `{$table}` (`active_name`)");
            }
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        return collect(DB::select("SHOW INDEX FROM `{$table}`"))
            ->contains('Key_name', $index);
    }
};
