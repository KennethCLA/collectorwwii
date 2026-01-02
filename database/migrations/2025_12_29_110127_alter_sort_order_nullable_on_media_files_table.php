<?php
// database/migrations/xxxx_xx_xx_xxxxxx_alter_sort_order_nullable_on_media_files_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('media_files', function (Blueprint $table) {
            $table->integer('sort_order')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('media_files', function (Blueprint $table) {
            $table->integer('sort_order')->nullable(false)->default(0)->change();
        });
    }
};
