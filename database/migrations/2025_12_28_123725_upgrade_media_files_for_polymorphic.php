<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Columns: add only if missing (migration may have partially run)
        Schema::table('media_files', function (Blueprint $table) {
            if (!Schema::hasColumn('media_files', 'disk')) {
                $table->string('disk', 50)->default('b2')->after('id');
            }
            if (!Schema::hasColumn('media_files', 'path')) {
                $table->string('path', 1024)->nullable()->after('disk');
            }
            if (!Schema::hasColumn('media_files', 'mime_type')) {
                $table->string('mime_type', 255)->nullable()->after('path');
            }
            if (!Schema::hasColumn('media_files', 'size')) {
                $table->unsignedBigInteger('size')->nullable()->after('mime_type');
            }
            if (!Schema::hasColumn('media_files', 'original_name')) {
                $table->string('original_name', 255)->nullable()->after('size');
            }
            if (!Schema::hasColumn('media_files', 'checksum')) {
                $table->char('checksum', 64)->nullable()->after('original_name');
            }
            if (!Schema::hasColumn('media_files', 'attachable_type')) {
                $table->string('attachable_type', 255)->nullable()->after('checksum');
            }
            if (!Schema::hasColumn('media_files', 'attachable_id')) {
                $table->unsignedBigInteger('attachable_id')->nullable()->after('attachable_type');
            }
            if (!Schema::hasColumn('media_files', 'collection')) {
                $table->string('collection', 50)->default('default')->after('attachable_id');
            }
            if (!Schema::hasColumn('media_files', 'is_main')) {
                $table->boolean('is_main')->default(false)->after('collection');
            }
            if (!Schema::hasColumn('media_files', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_main');
            }
        });

        // 2) Index: attachable composite (only if missing)
        $hasAttachableIdx = DB::selectOne("
            SELECT 1
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
              AND table_name = 'media_files'
              AND index_name = 'media_attachable_collection_idx'
            LIMIT 1
        ");

        if (!$hasAttachableIdx) {
            Schema::table('media_files', function (Blueprint $table) {
                $table->index(['attachable_type', 'attachable_id', 'collection'], 'media_attachable_collection_idx');
            });
        }

        // 3) Prefix index for (disk, path) (only if missing)
        $hasDiskPathIdx = DB::selectOne("
            SELECT 1
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
              AND table_name = 'media_files'
              AND index_name = 'media_disk_path_idx'
            LIMIT 1
        ");

        if (!$hasDiskPathIdx) {
            DB::statement('CREATE INDEX media_disk_path_idx ON media_files (disk, path(191))');
        }
    }

    public function down(): void
    {
        // Drop indexes if they exist
        DB::statement("DROP INDEX media_disk_path_idx ON media_files");

        Schema::table('media_files', function (Blueprint $table) {
            $table->dropIndex('media_attachable_collection_idx');

            $table->dropColumn([
                'disk',
                'path',
                'mime_type',
                'size',
                'original_name',
                'checksum',
                'attachable_type',
                'attachable_id',
                'collection',
                'is_main',
                'sort_order',
            ]);
        });
    }
};
