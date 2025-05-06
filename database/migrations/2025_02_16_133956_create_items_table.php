<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('item_categories')->onDelete('set null');
            $table->foreignId('origin_id')->nullable()->constrained('item_origins')->onDelete('set null');
            $table->foreignId('nationality_id')->nullable()->constrained('item_nationalities')->onDelete('set null');
            $table->foreignId('organization_id')->nullable()->constrained('item_organizations')->onDelete('set null');
            $table->decimal('purchase_price', 19, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('purchase_location')->nullable();
            $table->longText('notes')->nullable();
            $table->text('storage_location')->nullable();
            $table->decimal('current_price', 19, 2)->nullable();
            $table->boolean('for_sale')->default(0);
            $table->decimal('selling_price', 19, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Extra indexen
            $table->index('title');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
