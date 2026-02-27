<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('magazines', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->string('subtitle')->nullable()->after('title');
            $table->string('publisher')->nullable()->after('subtitle');
            $table->integer('issue_number')->nullable()->after('publisher');
            $table->integer('issue_year')->nullable()->after('issue_number');
            $table->text('description')->nullable()->after('issue_year');
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
        Schema::table('magazines', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'subtitle', 'publisher', 'issue_number', 'issue_year',
                'description', 'purchase_date', 'purchase_price', 'for_sale',
                'selling_price', 'notes', 'deleted_at',
            ]);
        });
    }
};
