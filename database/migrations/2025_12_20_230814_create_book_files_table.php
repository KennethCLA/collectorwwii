<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('book_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('book_id')->constrained()->cascadeOnDelete();

            $table->string('type', 20); // 'image' | 'pdf'
            $table->string('title')->nullable();

            // B2 path: bv. "books/123/cover.jpg" of "books/123/files/scan.pdf"
            $table->string('path');

            // Alleen relevant voor images
            $table->boolean('is_main')->default(false);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['book_id', 'type']);
            $table->index(['book_id', 'type', 'is_main']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_files');
    }
};
