<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'books', 'items', 'banknotes', 'coins',
        'magazines', 'newspapers', 'postcards', 'stamps',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('condition', 50)->nullable()->after('for_sale');
                $table->date('sold_at')->nullable()->after('selling_price');
                $table->decimal('sold_price', 10, 2)->nullable()->after('sold_at');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn(['condition', 'sold_at', 'sold_price']);
            });
        }
    }
};
