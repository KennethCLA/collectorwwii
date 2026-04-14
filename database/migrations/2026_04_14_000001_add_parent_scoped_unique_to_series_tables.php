<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'magazine_series'  => 'magazine_series_name_parent_unique',
            'newspaper_series' => 'newspaper_series_name_parent_unique',
        ] as $table => $indexName) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            DB::statement(
                "CREATE UNIQUE INDEX `{$indexName}` ON `{$table}` (`name`, `parent_id`)"
            );
        }
    }

    public function down(): void
    {
        foreach ([
            'magazine_series'  => 'magazine_series_name_parent_unique',
            'newspaper_series' => 'newspaper_series_name_parent_unique',
        ] as $table => $indexName) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            DB::statement("DROP INDEX `{$indexName}` ON `{$table}`");
        }
    }
};
