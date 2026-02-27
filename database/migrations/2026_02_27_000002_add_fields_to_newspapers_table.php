<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newspapers', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->string('publisher')->nullable()->after('title');
            $table->date('publication_date')->nullable()->after('publisher');
            $table->text('description')->nullable()->after('publication_date');
            $table->date('purchase_date')->nullable()->after('description');
            $table->decimal('purchase_price', 10, 2)->nullable()->after('purchase_date');
            $table->boolean('for_sale')->default(false)->after('purchase_price');
            $table->decimal('selling_price', 10, 2)->nullable()->after('for_sale');
            $table->text('notes')->nullable()->after('selling_price');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('newspapers', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'publisher', 'publication_date', 'description',
                'purchase_date', 'purchase_price', 'for_sale', 'selling_price',
                'notes', 'deleted_at',
            ]);
        });
    }
};
