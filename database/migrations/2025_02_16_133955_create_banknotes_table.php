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
        Schema::create('banknotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId('nominal_value_id')->constrained('nominal_values')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->foreignId('time_period_id')->constrained('banknote_time_periods')->onDelete('cascade');
            $table->foreignId('series_id')->constrained('banknote_series')->onDelete('cascade');
            $table->foreignId('head_of_state_id')->nullable()->constrained('heads_of_state')->onDelete('set null');
            $table->foreignId('colour_id')->nullable()->constrained('colours')->onDelete('set null');
            $table->foreignId('designer_id')->nullable()->constrained('banknote_designers')->onDelete('set null');
            $table->foreignId('watermark_id')->nullable()->constrained('banknote_watermarks')->onDelete('set null');
            $table->text('number_on_note')->nullable();
            $table->year('year')->nullable();
            $table->text('variation')->nullable();
            $table->text('special_features')->nullable();
            $table->text('number_jaeger')->nullable();
            $table->date('date_of_issue')->nullable();
            $table->text('front_image')->nullable();
            $table->text('front_text')->nullable();
            $table->text('reverse_image')->nullable();
            $table->text('reverse_text')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('print_run')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchasing_price', 10, 2)->nullable();
            $table->decimal('current_value', 10, 2)->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->text('location_detail')->nullable();
            $table->text('personal_remarks')->nullable();
            $table->tinyInteger('for_sale')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banknotes');
    }
};
