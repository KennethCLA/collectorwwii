<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (! Schema::hasColumn('books', 'origin_id')) {
                $table->foreignId('origin_id')
                    ->nullable()
                    ->after('purchase_price')
                    ->constrained('origins')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'origin_id')) {
                $table->dropForeign(['origin_id']);
                $table->dropColumn('origin_id');
            }
        });
    }
};
