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
        Schema::create('stamps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->integer('year')->nullable();
            $table->foreignId('type_id')->nullable()->constrained('stamp_types')->onDelete('set null');
            $table->text('michel_number')->nullable();
            $table->text('yvert_tellier_number')->nullable();
            $table->foreignId('nominal_value_id')->nullable()->constrained('nominal_values')->onDelete('set null');
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('set null');
            $table->text('date_of_issue')->nullable();
            $table->text('occasion')->nullable();
            $table->text('illustration')->nullable();
            $table->text('special_features')->nullable();
            $table->boolean('mnh')->nullable();
            $table->boolean('hinged')->nullable();
            $table->boolean('postmarked')->nullable();
            $table->boolean('special_postmark')->nullable();
            $table->text('postmark_date')->nullable();
            $table->text('postmark_location')->nullable();
            $table->text('postmark_text')->nullable();
            $table->foreignId('designer_id')->nullable()->constrained('stamp_designers')->onDelete('set null');
            $table->foreignId('colour_id')->nullable()->constrained('colours')->onDelete('set null');
            $table->foreignId('print_type_id')->nullable()->constrained('print_types')->onDelete('set null');
            $table->foreignId('watermark_id')->nullable()->constrained('stamp_watermarks')->onDelete('set null');
            $table->foreignId('gum_id')->nullable()->constrained('stamp_gums')->onDelete('set null');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->boolean('perforation')->nullable();
            $table->foreignId('perforation_id')->nullable()->constrained('stamp_perforations')->onDelete('set null');
            $table->foreignId('printing_house_id')->nullable()->constrained('stamp_printing_houses')->onDelete('set null');
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
        Schema::dropIfExists('stamps');
    }
};
