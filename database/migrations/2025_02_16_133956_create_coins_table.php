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
        Schema::create('coins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId('nominal_value_id')->constrained('nominal_values')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->text('time_period')->nullable();
            $table->foreignId('head_of_state_id')->nullable()->constrained('heads_of_state')->onDelete('set null');
            $table->year('year')->nullable();
            $table->foreignId('strike_mark_id')->nullable()->constrained('coin_strike_marks')->onDelete('set null');
            $table->text('number_jaeger')->nullable();
            $table->date('date_of_issue')->nullable();
            $table->foreignId('occasion_id')->nullable()->constrained('coin_occasions')->onDelete('set null');
            $table->foreignId('front_image_id')->nullable()->constrained('coin_front_images')->onDelete('set null');
            $table->foreignId('front_text_id')->nullable()->constrained('coin_front_texts')->onDelete('set null');
            $table->foreignId('reverse_image_id')->nullable()->constrained('coin_reverse_images')->onDelete('set null');
            $table->foreignId('reverse_text_id')->nullable()->constrained('coin_reverse_texts')->onDelete('set null');
            $table->foreignId('rim_id')->nullable()->constrained('coin_rims')->onDelete('set null');
            $table->foreignId('rim_text_id')->nullable()->constrained('coin_rim_texts')->onDelete('set null');
            $table->text('special_features')->nullable();
            $table->foreignId('designer_id')->nullable()->constrained('coin_designers')->onDelete('set null');
            $table->foreignId('material_id')->nullable()->constrained('coin_materials')->onDelete('set null');
            $table->decimal('gold_silver_content', 10, 2)->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('diameter', 10, 2)->nullable();
            $table->decimal('thickness', 10, 2)->nullable();
            $table->foreignId('shape_id')->nullable()->constrained('coin_shapes')->onDelete('set null');
            $table->integer('run')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchasing_price', 10, 2)->nullable();
            $table->decimal('current_value', 10, 2)->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->text('location_detail')->nullable();
            $table->text('personal_remarks')->nullable();
            $table->tinyInteger('for_sale')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Extra indexen
            $table->index('country_id');
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coins');
    }
};
