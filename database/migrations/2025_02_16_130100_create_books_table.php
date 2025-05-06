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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn', 20)->nullable();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('title_first_edition')->nullable();
            $table->string('subtitle_first_edition')->nullable();
            $table->longText('description')->nullable();
            $table->string('translator')->nullable();
            $table->year('copyright_year')->nullable();
            $table->string('issue_number')->nullable();
            $table->integer('issue_year')->nullable();
            $table->foreignId('series_id')->nullable()->constrained('book_series')->onDelete('set null');
            $table->string('series_number')->nullable();
            $table->integer('pages')->nullable();
            $table->foreignId('cover_id')->nullable()->constrained('book_covers')->onDelete('set null');
            $table->foreignId('topic_id')->nullable()->constrained('book_topics')->onDelete('set null');
            $table->year('copyright_year_first_issue')->nullable();
            $table->string('publisher_name')->nullable();
            $table->string('publisher_first_issue')->nullable();
            $table->decimal('purchase_price', 19, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->longText('notes')->nullable();
            $table->text('storage_location')->nullable();
            $table->boolean('for_sale')->default(false);
            $table->decimal('selling_price', 19, 2)->nullable();
            $table->integer('weight')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('thickness')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
