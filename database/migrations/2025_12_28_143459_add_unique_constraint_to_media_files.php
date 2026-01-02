<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Ensure checksum exists (it does) and backfill deterministic values for existing rows
        // For existing data we don't have true file hashes; we use sha256(disk + '|' + path)
        DB::statement("
            UPDATE media_files
            SET checksum = SHA2(CONCAT(COALESCE(disk,''),'|',COALESCE(path,'')), 256)
            WHERE checksum IS NULL OR checksum = ''
        ");

        // 2) Add a UNIQUE constraint that fits within MySQL index limits
        Schema::table('media_files', function (Blueprint $table) {
            // Defensive: only add if not present
            // Laravel doesn't have a direct "hasIndex" helper; we'll rely on DB check
        });

        $exists = DB::selectOne("
            SELECT 1
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
              AND table_name = 'media_files'
              AND index_name = 'media_unique_attachable_collection_checksum'
            LIMIT 1
        ");

        if (!$exists) {
            Schema::table('media_files', function (Blueprint $table) {
                $table->unique(
                    ['attachable_type', 'attachable_id', 'collection', 'checksum'],
                    'media_unique_attachable_collection_checksum'
                );
            });
        }
    }

    public function down(): void
    {
        $exists = DB::selectOne("
            SELECT 1
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
              AND table_name = 'media_files'
              AND index_name = 'media_unique_attachable_collection_checksum'
            LIMIT 1
        ");

        if ($exists) {
            Schema::table('media_files', function (Blueprint $table) {
                $table->dropUnique('media_unique_attachable_collection_checksum');
            });
        }
    }
};
