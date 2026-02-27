<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('newspapers')) {
            return;
        }

        Schema::table('newspapers', function (Blueprint $table) {
            if (!Schema::hasColumn('newspapers', 'title')) {
                $table->string('title');
            }
            if (!Schema::hasColumn('newspapers', 'publisher')) {
                $table->string('publisher')->nullable();
            }
            if (!Schema::hasColumn('newspapers', 'publication_date')) {
                $table->date('publication_date')->nullable();
            }
            if (!Schema::hasColumn('newspapers', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('newspapers', 'purchase_date')) {
                $table->date('purchase_date')->nullable();
            }
            if (!Schema::hasColumn('newspapers', 'purchase_price')) {
                $table->decimal('purchase_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('newspapers', 'for_sale')) {
                $table->boolean('for_sale')->default(false);
            }
            if (!Schema::hasColumn('newspapers', 'selling_price')) {
                $table->decimal('selling_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('newspapers', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('newspapers', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('newspapers')) {
            return;
        }

        Schema::table('newspapers', function (Blueprint $table) {
            $drop = array_values(array_filter([
                Schema::hasColumn('newspapers', 'title') ? 'title' : null,
                Schema::hasColumn('newspapers', 'publisher') ? 'publisher' : null,
                Schema::hasColumn('newspapers', 'publication_date') ? 'publication_date' : null,
                Schema::hasColumn('newspapers', 'description') ? 'description' : null,
                Schema::hasColumn('newspapers', 'purchase_date') ? 'purchase_date' : null,
                Schema::hasColumn('newspapers', 'purchase_price') ? 'purchase_price' : null,
                Schema::hasColumn('newspapers', 'for_sale') ? 'for_sale' : null,
                Schema::hasColumn('newspapers', 'selling_price') ? 'selling_price' : null,
                Schema::hasColumn('newspapers', 'notes') ? 'notes' : null,
                Schema::hasColumn('newspapers', 'deleted_at') ? 'deleted_at' : null,
            ]));

            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
