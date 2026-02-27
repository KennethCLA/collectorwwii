<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop FK on items.origin_id → item_origins if it exists
        $db = DB::getDatabaseName();
        $fk = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'items'
              AND COLUMN_NAME = 'origin_id' AND REFERENCED_TABLE_NAME = 'item_origins'
            LIMIT 1
        ", [$db]);

        if ($fk) {
            Schema::table('items', function (Blueprint $table) use ($fk) {
                $table->dropForeign($fk->CONSTRAINT_NAME);
            });
        }

        // Rename preserves all data, generated columns, and indexes
        Schema::rename('item_origins', 'origins');

        // Re-add FK on items.origin_id → origins.id
        Schema::table('items', function (Blueprint $table) {
            $table->foreign('origin_id')->references('id')->on('origins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        $db = DB::getDatabaseName();
        $fk = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'items'
              AND COLUMN_NAME = 'origin_id' AND REFERENCED_TABLE_NAME = 'origins'
            LIMIT 1
        ", [$db]);

        if ($fk) {
            Schema::table('items', function (Blueprint $table) use ($fk) {
                $table->dropForeign($fk->CONSTRAINT_NAME);
            });
        }

        Schema::rename('origins', 'item_origins');

        Schema::table('items', function (Blueprint $table) {
            $table->foreign('origin_id')->references('id')->on('item_origins')->nullOnDelete();
        });
    }
};
