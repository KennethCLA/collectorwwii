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
        Schema::create('postcards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->integer('year')->nullable();
            $table->text('michel_number')->nullable();
            $table->foreignId('nominal_value_id')->nullable()->constrained('nominal_values')->onDelete('set null');
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('set null');
            $table->foreignId('postcard_type_id')->nullable()->constrained('postcard_types')->onDelete('set null');
            $table->text('date_of_issue')->nullable();
            $table->foreignId('valuation_image_id')->nullable()->constrained('postcard_valuation_images')->onDelete('set null');
            $table->text('occasion')->nullable();
            $table->text('front_image')->nullable();
            $table->text('special_features')->nullable();
            $table->boolean('unstamped')->nullable();
            $table->boolean('stamped')->nullable();
            $table->boolean('special_stamp')->nullable();
            $table->text('stamp_text')->nullable();
            $table->text('stamp_date')->nullable();
            $table->text('stamp_location')->nullable();
            $table->foreignId('colour_id')->nullable()->constrained('colours')->onDelete('set null');
            $table->boolean('perforation')->nullable();
            $table->foreignId('print_type_id')->nullable()->constrained('print_types')->onDelete('set null');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('print_run')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchasing_price', 10, 2)->nullable();
            $table->decimal('current_value', 10, 2)->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->text('location_detail')->nullable();
            $table->text('personal_remarks')->nullable();
            $table->boolean('for_sale')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postcards');
    }
};
