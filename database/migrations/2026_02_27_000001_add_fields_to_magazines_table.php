<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('magazines')) {
            return;
        }

        Schema::table('magazines', function (Blueprint $table) {
            if (!Schema::hasColumn('magazines', 'title')) {
                $table->string('title');
            }
            if (!Schema::hasColumn('magazines', 'subtitle')) {
                $table->string('subtitle')->nullable();
            }
            if (!Schema::hasColumn('magazines', 'publisher')) {
                $table->string('publisher')->nullable();
            }
            if (!Schema::hasColumn('magazines', 'issue_number')) {
                $table->integer('issue_number')->nullable();
            }
            if (!Schema::hasColumn('magazines', 'issue_year')) {
                $table->integer('issue_year')->nullable();
            }
            if (!Schema::hasColumn('magazines', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('magazines', 'purchase_date')) {
                $table->date('purchase_date')->nullable();
            }
            if (!Schema::hasColumn('magazines', 'purchase_price')) {
                $table->decimal('purchase_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('magazines', 'for_sale')) {
                $table->boolean('for_sale')->default(false);
            }
            if (!Schema::hasColumn('magazines', 'selling_price')) {
                $table->decimal('selling_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('magazines', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('magazines', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('magazines')) {
            return;
        }

        Schema::table('magazines', function (Blueprint $table) {
            $drop = array_values(array_filter([
                Schema::hasColumn('magazines', 'title') ? 'title' : null,
                Schema::hasColumn('magazines', 'subtitle') ? 'subtitle' : null,
                Schema::hasColumn('magazines', 'publisher') ? 'publisher' : null,
                Schema::hasColumn('magazines', 'issue_number') ? 'issue_number' : null,
                Schema::hasColumn('magazines', 'issue_year') ? 'issue_year' : null,
                Schema::hasColumn('magazines', 'description') ? 'description' : null,
                Schema::hasColumn('magazines', 'purchase_date') ? 'purchase_date' : null,
                Schema::hasColumn('magazines', 'purchase_price') ? 'purchase_price' : null,
                Schema::hasColumn('magazines', 'for_sale') ? 'for_sale' : null,
                Schema::hasColumn('magazines', 'selling_price') ? 'selling_price' : null,
                Schema::hasColumn('magazines', 'notes') ? 'notes' : null,
                Schema::hasColumn('magazines', 'deleted_at') ? 'deleted_at' : null,
            ]));

            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
