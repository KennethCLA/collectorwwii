<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newspapers', function (Blueprint $table) {
            $table->foreignId('series_id')->nullable()->after('title')
                ->constrained('newspaper_series')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('newspapers', function (Blueprint $table) {
            $table->dropForeign(['series_id']);
            $table->dropColumn('series_id');
        });
    }
};
