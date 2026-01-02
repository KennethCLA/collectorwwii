<?php
// database/migrations/2025_12_28_145747_drop_legacy_columns_from_media_files.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_files', function (Blueprint $table) {
            $drops = [];

            foreach (['file_path', 'file_type', 'file_size', 'associated_table', 'associated_id'] as $col) {
                if (Schema::hasColumn('media_files', $col)) {
                    $drops[] = $col;
                }
            }

            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }

    public function down(): void
    {
        // Laat leeg (of voeg ze terug toe als je echt rollback wil ondersteunen)
    }
};
