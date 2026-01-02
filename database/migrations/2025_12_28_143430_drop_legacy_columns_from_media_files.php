<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('media_files', function (Blueprint $table) {
            if (Schema::hasColumn('media_files', 'file_path')) $table->dropColumn('file_path');
            if (Schema::hasColumn('media_files', 'file_type')) $table->dropColumn('file_type');
            if (Schema::hasColumn('media_files', 'file_size')) $table->dropColumn('file_size');
            if (Schema::hasColumn('media_files', 'associated_table')) $table->dropColumn('associated_table');
            if (Schema::hasColumn('media_files', 'associated_id')) $table->dropColumn('associated_id');
        });
    }

    public function down(): void
    {
        // Not recommended to restore legacy columns once removed
    }
};
