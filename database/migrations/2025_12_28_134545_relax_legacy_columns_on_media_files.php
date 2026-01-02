<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('media_files', function (Blueprint $table) {
            // Make legacy columns nullable so new system can insert without them
            $table->string('file_path', 255)->nullable()->change();
            $table->string('file_type', 255)->nullable()->change();
            $table->integer('file_size')->nullable()->change();
            $table->string('associated_table', 255)->nullable()->change();
            $table->unsignedInteger('associated_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('media_files', function (Blueprint $table) {
            // Reverting to NOT NULL is risky once new rows exist; keep it simple:
            $table->string('file_path', 255)->nullable(false)->change();
            $table->string('file_type', 255)->nullable(false)->change();
            // Others can remain nullable
        });
    }
};
